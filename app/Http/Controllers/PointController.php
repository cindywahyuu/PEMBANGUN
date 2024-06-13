<?php

namespace App\Http\Controllers;

use App\Models\Points;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PointController extends Controller
{
    public function __construct()
    {
        $this->point = new Points();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //index menampilkan seluruh data
        //points = memanggil function point pada model
        $points = $this->point->points();

        foreach ($points as $p) {
            $feature[] = [
            'type' => 'Feature',
            'geometry' => json_decode($p->geom),
            //Json decode = mengubah string json menjadi php
            'properties' => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'image' => $p->image,
                // mengambil nilai properti image dari objek $p dan memasukkannya ke dalam array properties
                'created_at' => $p->created_at,
                'updated_at' => $p->updated_at
            ]
            ];
        }
        // dd($points);
        // dd untuk mengecek data
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $feature,
        ]);
        // membuat dan memunculkan api dalam format json
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
    $filename = time() . '_point.' . $image->getClientOriginalExtension();
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

       // Create Point
       if (!$this->point->create($data)){
        return redirect()-> back()->with('error', 'Failed to create point');
    }

    // Redirect to Map
    return redirect()->back()->with('success', 'Point created successfully');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //Mengambil data point berdasarkan ID
        $point = $this->point->point($id);

         // Melakukan iterasi melalui setiap data point yang ditemukan
        foreach ($point as $p) {

            // Menambahkan setiap data point ke dalam array fitur sebagai objek GeoJSON
            $feature[] = [
                'type' => 'Feature',
                'geometry' => json_decode($p->geom), // Mendekode data geometris dari format JSON
                'properties' => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'image' => $p->image,
                    'created_at' => $p->created_at,
                    'updated_at' => $p->updated_at
                ]
                ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $feature,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $point = $this->point->find($id);

        $data = [
            'title' => 'Edit Point',
            'point' => $point,
            'id' => $id
        ];

        return view('edit-point', $data);
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
    $filename = time() . '_point.' . $image->getClientOriginalExtension();
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

       // Update Point
       if (!$this->point->find($id)->update($data)) {
        return redirect()-> back()->with('error', 'Failed to create point');
    }

    // Redirect to Map
    return redirect()->back()->with('success', 'Update created successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
   // get image
   $image = $this->point->find($id)->image;

   // delete point
   if (!$this->point->destroy($id)) {
       return redirect()->back()->with('error', 'Failed to delete point');
   }

   //delete image

   if ($image != null) {
       unlink('storage/images/' . $image);
   }


   //redirect to map
   return redirect()->back()->with('success', 'Point deleted successfully');

   }

   public function table()
   {
       $points = $this->point->points();

       //dd($points);

       $data = [
           'title' => 'Table Point',
           'points' => $points
       ];

       return view ('table-point', $data);
   }

    }

