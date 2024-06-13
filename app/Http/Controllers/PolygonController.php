<?php

namespace App\Http\Controllers;
use App\Models\Polygons;
use Illuminate\Http\Request;

class PolygonController extends Controller
{
    public function __construct()
    {
        $this->polygon = new Polygons();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       //
        $polygon = $this-> polygon-> polygons();
        // polygons memanggil func polygon yang ada di model, shg  akan muncul koordinat

        foreach ($polygon as  $polygon) {
            $feature[] = [
                'type' => 'Feature',
                'geometry' => json_decode( $polygon->geom),
                // json_decode untuk mengubah string JSON menjadi variabel php agar mudah dibaca
                // encode kebalikannya, yaitu untuk mengubah var php menjdi string
                'properties' => [
                    'id' => $polygon->id,
                    'name' => $polygon->name,
                    'description' => $polygon->description,
                    'image' => $polygon->image,
                    'created_at' => $polygon->created_at,
                    'updated_at' => $polygon->updated_at
                    // ctrl+d untuk mengubah serempak
                ]
            ];
        }
        // all untuk memanggil seluruh data
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $feature,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validate request
        $request->validate( [
            'name' => 'required',
            'geom' => 'required',
            'image' => 'mimes:jpg,jpeg,png,tiff,gif|max:10000' //10MB
        ],
        [
            'name.required' => 'Name is required',
            'geom.required' => 'Location is required',
            'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, tif, gif',
            'image.max' => 'Image must not exceed 10MB'
        ]);

         // upload image
  if ($request->hasFile('image')) {
    $image = $request->file('image');
    $filename = time() . '_polygon.' . $image->getClientOriginalExtension();
    // [nama akan di rename dengan waktu upload_jenis vektor]
    $image->move('storage/images', $filename);
  }else {
    $filename = null;
  }


        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'geom' => $request->geom,
            'image' => $filename
        ];

        // Create polygon
        if (!$this->polygon->create($data)){
            return redirect()-> back()->with('error', 'Failed to create polygon');
        }

        // Redirect to Map
        return redirect()->back()->with('success', 'Polygon created succesfullt');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         $polygon = $this-> polygon-> polygons($id);
         // polygons memanggil func polygon yang ada di model, shg  akan muncul koordinat

         foreach ($polygon as  $polygon) {
             $feature[] = [
                 'type' => 'Feature',
                 'geometry' => json_decode( $polygon->geom),
                 // json_decode untuk mengubah string JSON menjadi variabel php agar mudah dibaca
                 // encode kebalikannya, yaitu untuk mengubah var php menjdi string
                 'properties' => [
                     'id' => $polygon->id,
                     'name' => $polygon->name,
                     'description' => $polygon->description,
                     'image' => $polygon->image,
                     'created_at' => $polygon->created_at,
                     'updated_at' => $polygon->updated_at
                     // ctrl+d untuk mengubah serempak
                 ]
             ];
         }
         // all untuk memanggil seluruh data
         return response()->json([
             'type' => 'FeatureCollection',
             'features' => $feature,
         ]);
    }

    public function edit(string $id)
    {
        $polygon = $this->polygon->find($id);

        $data = [
            'title' => 'Edit Polygon',
            'polygon' => $polygon,
            'id' => $id
        ];

        return view('edit-polygon', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       // validate request
       $request->validate ( [
        'name' => 'required',
        'geom' => 'required',
        'image' => 'mimes:jpg,jpeg,png,tiff,gif|max:10000' //10MB
    ],
    [
        'name.required' => 'Name is Required',
        'geom.required' => 'Location is Required',
        'image.mimes' => 'Image must be a file of type: jpg, jpeg, png, tif, gif',
        'image.max' => 'Image must not exceed 10MB'
    ]);



   // Create Folder Image
   if (!is_dir('storage/images')) {
    mkdir('storage/images', 0777);
//    jika tidak tersedia folder image, maka akan dilakukan pembuatan folder image dengan permission 0777
   }

   // upload image
if ($request->hasFile('image')) {
$image = $request->file('image');
// mengambil file dan menyimpannya dalam variabel
$filename = time() . '_polygon.' . $image->getClientOriginalExtension();
// [nama akan di rename dengan waktu upload_jenis vektor]
$image->move('storage/images', $filename);

//delete image
$image_old = $request->image_old;
if ($image_old !=null) {
unlink('storage/images/' . $image_old);
}

} else {
$filename = $request->image_old;
}

$data = [
'name' => $request->name,
'description' => $request->description,
'geom' => $request->geom,
'image' => $filename
];

   // Update Polygon
   if (!$this->polygon->find($id)->update($data)) {
    return redirect()-> back()->with('error', 'Failed to create polygon');
}

// Redirect to Map
return redirect()->back()->with('success', 'Update created successfully');
}

    public function destroy(string $id)
    {
         // get image
   $image = $this->polygon->find($id)->image;

   // delete polygon
   if (!$this->polygon->destroy($id)) {
       return redirect()->back()->with('error', 'Failed to delete polygon');
   }

   //delete image
   if ($image !=null) {
       unlink('storage/images/' . $image);
   }

   //redirect to map
   return redirect()->back()->with('success', 'Polygon deleted successfully');

   }

   public function table()
   {
       $polygons = $this->polygon->all();

       //dd($polygons);

       $data = [
           'title' => 'Table Polygon',
           'polygons' => $polygons
       ];

       return view ('table-polygon', $data);
   }

}
