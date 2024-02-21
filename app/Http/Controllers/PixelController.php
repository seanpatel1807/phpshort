<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pixel;
use Illuminate\Support\Facades\DB;

class PixelController extends Controller
{
    public function create()
    {
        return view('pixels.create');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
        ]);
        $user = auth()->user();

        Pixel::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'users_id' => $user->id,
        ]);
        

        return redirect()->route('user.pixel')->with('success', 'Pixel data added successfully!');
    }
    public function index()
    {
        $pixels = DB::table('pixels')
        ->select(
            'pixels.*',
            DB::raw('(SELECT COUNT(*) FROM links WHERE links.pixels_id = pixels.id) as links_count')
        )
        ->get();
        return view('user.pixel', compact('pixels'));
    }
    public function destroy($id)
    {
        $pixel = Pixel::find($id);
        $pixel->delete();

        return redirect()->route('user.pixel')->with('success', 'Pixel deleted successfully!');
    }
    public function edit($id)
    {
        $pixel = Pixel::find($id);
        return view('pixels.edit', compact('pixel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
        ]);

        $pixel = Pixel::find($id);
        $pixel->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
        ]);

        return redirect()->route('user.pixel')->with('success', 'Pixel updated successfully!');
    }    
    public function data()
    {

    $user = DB::table('pixels')
    ->join('users', 'users.id', '=', 'pixels.users_id')
    ->select(
        'pixels.*',
        'users.name as user_name',
        'users.email as user_email',
        DB::raw('(SELECT COUNT(*) FROM links WHERE links.pixels_id = pixels.id) as links_count')
    )
    ->get();
       
        return view('pixels', compact('user'));
    }
}
