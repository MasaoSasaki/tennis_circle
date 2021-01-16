<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\User;
use Illuminate\Support\Facades\Session;


class AlbumController extends Controller
{
  public function index()
  {
    $albums = Album::all();
    return view('admin/album/index', compact('albums'));
  }

  public function create()
  {
    $album = new Album();
    $names = array();
    forEach(User::all() as $user) {
      array_push($names, $user['last_name'].' '.$user['first_name']);
    }
    return view('admin/album/create', compact('album', 'names'));
  }

  public function store(Request $request)
  {
    $album = new Album;
    $album['title'] = $request['title'];
    $album['body'] = $request['body'];
    $album['isPublished'] = isset($request['isPublished']) ? true : false;
    // $album->save();
    logger($request);
    if($request->file('files')) {
      $albumFolder = preg_replace('/\s+|-|:|/', '', $album->created_at);
      forEach($request->file('files') as $image) {
        Storage::disk('s3')->putFile($albumFolder, $image, 'public');
      }
    }
    return redirect('admin/albums')->with('success', '作成が完了しました。');
  }

  public function edit($id)
  {
    $album = Album::findOrFail($id);
    $folderName = getFolderName($album);
    $filePaths = Storage::disk('s3')->files($folderName);
    $fileNames = array();
    forEach($filePaths as $filePath) {
      array_push($fileNames, getFileNameOfFilePath($filePath));
    }
    $names = array();
    forEach(User::all() as $user) {
      array_push($names, $user['last_name'].' '.$user['first_name']);
    }
    return view('admin/album/edit', compact('fileNames', 'album', 'folderName', 'names'));
  }

  public function update(Request $request, $id)
  {
    $album = Album::findOrFail($id);
    $album['title'] = $request['title'];
    $album['body'] = $request['body'];
    $album['isPublished'] = isset($request['isPublished']) ? true : false;
    // $album->save();
    logger($request);
    return redirect('admin/albums')->with('success', '更新が完了しました。');
  }

  public function destroy($id)
  {
    $album = Album::findOrFail($id);
    $albumFolder = preg_replace('/\s+|-|:|/', '', $album->created_at);
    Storage::disk('s3')->deleteDirectory($albumFolder);
    $album->delete();
    return redirect('admin/albums')->with('success', '削除が完了しました。');
  }
}
