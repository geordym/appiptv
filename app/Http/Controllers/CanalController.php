<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaRegistro;
use Illuminate\Http\Request;

use App\Models\Canal;
use Exception;
use Illuminate\Support\Facades\Response;
class CanalController extends Controller
{
    //
    public $xml_ruta = "";
    public $xml_empty_ruta = "";

    public function __construct()
    {
        $this->xml_ruta = env('XML_FILE_DIR', '/var/www/html/canales/canales.xml');
        $this->xml_empty_ruta = env('XML_EMPTY_FILE_DIR', '/var/www/html/canales/canales_empty.xml');
    }


    private function cargarCanales()
    {
        $xml = $this->xml_ruta;
        $canales = [];

        //dd($xml);
        try {
            if (file_exists($xml)) {

                $xml = simplexml_load_file($xml);
                if ($xml) {
                    if (isset($xml->item)) {
                        foreach ($xml->item as $item) {
                            $keyValue = strval($item->key);
                            $numberValue = strval($item->number); // Obtiene el valor de $item->number como una cadena
                            $valueValue = strval($item->value); // Obtiene el valor de $item->value como una cadena
                            $typeValue = strval($item->type); // Obtiene el valor de $item->type como una cadena

                            $canal = [
                                "key" => $keyValue,
                                "number" => $numberValue,
                                "value" => $valueValue,
                                "type" => $typeValue
                            ];

                            $canales[] = $canal;
                        }

                        return $canales;
                    } else {

                        echo "El elemento <item> no existe en el XML.";
                        return $canales;
                    }
                } else {
                    echo "No se pudo cargar el XML, no hay canales.";

                    return $canales;
                }
            }
        } catch (Exception $e) {
            return $canales;
        }
    }

    public function create(Request $request)
    {
        $key = $request->input('key');
        $value = $request->input('value');
        $type = $request->input('type');
        $number = $request->input('number');

        Canal::create([
            'key' =>   $key,
            'value' => $value,
            'type' => $type,
            'number' => $number

        ]);

        return redirect()->route('admin.canales')->with('success', 'El canal se ha creado con éxito.');
    }
    public function index(Request $request)
    {
        $canales_instalados = $this->cargarCanales();
        if ($canales_instalados === null) {
            $canales_instalados = [];
        }

        $canales = Canal::all()->toArray();

        usort($canales, function ($a, $b) {
            return $a['number'] <=> $b['number'];
        });

        return view('canales.index')->with('canales', $canales)->with('canales_instalados', $canales_instalados);
    }

    public function edit($id)
    {
        try {
            // Validar que el ID sea un número entero
            if (!is_numeric($id)) {
                return redirect()->route('admin.canales')->with('error', 'ID inválido.');
            }

            // Buscar el canal por ID
            $canal = Canal::find($id);

            // Validar si el canal existe
            if (!$canal) {
                return redirect()->route('admin.canales')->with('error', 'Canal no encontrado.');
            }

            // Retornar la vista de edición con el canal
            return view('canales.edit')->with('canal', $canal);

        } catch (\Exception $e) {
            // Manejo de cualquier otra excepción
            return redirect()->route('admin.canales')->with('error', 'Ocurrió un error al buscar el canal: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        // Encuentra el canal a actualizar por su ID
        $canal = Canal::find($id);

        // Actualiza los campos del canal con los datos del formulario
        $canal->key = $request->input('key');
        $canal->value = $request->input('value');
        $canal->type = $request->input('type');
        $canal->number = $request->input('number');
        $canal->habilitado = $request->input('habilitado');

        // Guarda el canal actualizado en la base de datos
        $canal->save();

        return redirect()->route('admin.canales')->with('success', 'El canal se ha actualizado.');
    }

    public function destroy($id)
    {
        try {
            $canal = Canal::find($id);

            if (!$canal) {
                return [
                    'errors' => 'El canal no existe.'
                ];
            }

            if ($canal->paquetes()->count() > 0) {
                return  'No se puede eliminar el canal porque está afiliado a uno o más paquetes. Desafílielo primero.';
            }

            $canal->delete();

            return 'El canal ha sido eliminado exitosamente.';

        } catch (\Exception $e) {
            return [
                'errors' => 'Ocurrió un error al intentar eliminar el canal: ' . $e->getMessage()
            ];
        }
    }



    public function generarXML()
{
    $canales_habilitados = Canal::where('habilitado', 1)->get();

    // Crear un nuevo objeto SimpleXMLElement con una raíz llamada "list"
    $xml = new \SimpleXMLElement('<?xml version="1.0"?><list></list>');

    foreach ($canales_habilitados as $canal) {
        $item1 = $xml->addChild('item');
        $item1->addChild('key', htmlspecialchars($canal->key, ENT_XML1, 'UTF-8'));
        $item1->addChild('value', htmlspecialchars($canal->value, ENT_XML1, 'UTF-8'));
        $item1->addChild('type', htmlspecialchars($canal->type, ENT_XML1, 'UTF-8'));

        if ($canal->number != 0) {
            $item1->addChild('number', $canal->number);
        }
    }

    $xmlString = $xml->asXML();

    try {
        $archivo_canales = fopen($this->xml_ruta, "w");
        fwrite($archivo_canales, $xmlString);
        fclose($archivo_canales);
    } catch (Exception $e) {
        echo 'Exception: ' . $e;
    }

    // Devolver una respuesta HTTP con el contenido XML
    return redirect()->route('admin.canales')->with('success', 'El archivo de canales se ha actualizado exitosamente.');
}

function removeDuplicateChannels($channels) {
    $uniqueChannels = [];
    $seenIds = [];

    foreach ($channels as $channel) {
        if (!in_array($channel->id, $seenIds)) {
            $uniqueChannels[] = $channel;
            $seenIds[] = $channel->id;
        }
    }

    return $uniqueChannels;
}

function filterChannelsByEnabled($channels) {
    return array_filter($channels, function($channel) {
        return $channel->habilitado == 1;
    });
}

public function generarXMLByCajaId($caja_id)
{
    $caja = Caja::with(['paquetes', 'paquetes.canales'])->where('id', $caja_id)->first();
        $paquetes = $caja->paquetes;

    $caja_canales = [];
    foreach($paquetes as $paquete){
        $canales = $paquete->canales;
        foreach($canales as $canal){
            $caja_canales[] = $canal;
        }
    }


    $caja_canales = $this->removeDuplicateChannels($caja_canales);
    $caja_canales = $this->filterChannelsByEnabled($caja_canales);

    usort($caja_canales, function($a, $b) {
        return $a->number <=> $b->number;
    });

    $xml = new \SimpleXMLElement('<?xml version="1.0"?><list></list>');

    foreach ($caja_canales as $canal) {
        $item1 = $xml->addChild('item');
        $item1->addChild('key', htmlspecialchars($canal->key, ENT_XML1, 'UTF-8'));
        $item1->addChild('value', htmlspecialchars($canal->value, ENT_XML1, 'UTF-8'));
        $item1->addChild('type', htmlspecialchars($canal->type, ENT_XML1, 'UTF-8'));

        if ($canal->number != 0) {
            $item1->addChild('number', $canal->number);
        }
    }

    $xmlString = $xml->asXML();

    return $xmlString;

}




    function retornarXML(Request $request)
    {

        $mac = $request->input('mac') ?? '00:00:00:00:00:00';
        $caja_registro = new CajaRegistro;
        $caja_registro->mac = $mac;
        $caja_registro->save();

        $caja = Caja::where('mac', $mac)->first();



        if (!$caja) {
            return response()->file($this->xml_empty_ruta);
        }

        $estado = $caja->estado;

        if ($estado === "activado") {
            $xmlString = $this->generarXMLByCajaId($caja->id);
            return response($xmlString, 200)
            ->header('Content-Type', 'text/xml');
        } else {
            return response()->file($this->xml_empty_ruta);
        }




    }




}
