<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('petugas/buku/index');
    }

    if ($request->file('image')) {
        $image_name = $request->file('image');
        // $image_name = $request->file('image')->store('images', 'public');
        $storage = new StorageClient([
            'keyFilePath' => public_path('key.json')
        ]);

        $bucketName = env('GOOGLE_CLOUD_BUCKET');
        $bucket = $storage->bucket($bucketName);

        //get filename with extension
        $filenamewithextension = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
        // $filenamewithextension = $request->file('image')->getClientOriginalName();

        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

        //get file extension
        $extension = $request->file('image')->getClientOriginalExtension();

        //filename to store
        $filenametostore = $filename . '_' . uniqid() . '.' . $extension;

        Storage::put('public/uploads/' . $filenametostore, fopen($request->file('image'), 'r+'));

        $filepath = storage_path('app/public/uploads/' . $filenametostore);

        $object = $bucket->upload(
            fopen($filepath, 'r'),
            [
                'predefinedAcl' => 'publicRead'
            ]
        );

        // delete file from local disk
        Storage::delete('public/uploads/' . $filenametostore);
    }
}
