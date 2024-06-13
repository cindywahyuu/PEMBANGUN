//?php
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;

// class GeoJsonController extends Controller
// {
//     public function index()
//     {
//         // URL GeoServer WFS endpoint
//         $wfsUrl = 'http://localhost:8080/geoserver/pgwl/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=pgwl%3Atable_tambang&outputFormat=application%2Fjson';
//         $params = [
//             'service' => 'WFS',
//             'version' => '1.0.0',
//             'request' => 'GetFeature',
//             'typeName' => 'your_layer',
//             'outputFormat' => 'application/json'
//         ];

//         // Fetch GeoJSON data from GeoServer
//         $response = Http::get($wfsUrl, $params);
//         $geojson = $response->json();

//         // Pass GeoJSON data to the view
//         return view('map', compact('geojson'));
//     }
// }


//?>

