<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index(){
        $menu = Menu::whereNull('parent_menu')
            ->with(['submenu' => fn ($q) => $q->orderBy('urutan_menu', 'asc')])
            ->orderBy('urutan_menu', 'asc')
            ->get();
        return view('backend.content.menu.list', compact('menu'));
    }

    public function order($idMenu, $idSwap){
        $menu = Menu::findOrFail($idMenu);
        $menuOrder = $menu->urutan_menu;

        $swap = Menu::findOrFail($idSwap);
        $swapOrder = $swap->urutan_menu;

        $menu->urutan_menu = $swapOrder;
        $swap->urutan_menu = $menuOrder;

        try {
            $menu->save();
            $swap->save();
            return redirect(route('menu.index'))->with('pesan', ['success', 'Berhasil ubah urutan menu']);
        } catch (\Exception $e) {
            return redirect(route('menu.index'))->with('pesan', ['danger', 'Gagal ubah urutan menu']);
        }
    }

    public function tambah(){
        $page = Page::where('status_page', 1)->get();
        $parent = Menu::whereNull('parent_menu')->where('status_menu', 1)->get();
        return view('backend.content.menu.formTambah', compact('page', 'parent'));
    }

    public function prosesTambah(Request $request){
        $this->validate($request, [
            'nama_menu' => 'required',
            'jenis_menu' => 'required',
            'target_menu' => 'required',
        ]);

        $parent_menu = $request->parent_menu;
        if ($parent_menu == null) {
            $urut = $this->getUrutanMenu();
        } else {
            $urut = $this->getUrutanMenu($parent_menu);
        }

        $url_menu = "";
        if($request->jenis_menu == "url" ){
            $url_menu = $request->link_url;
        } else {
            $url_menu = $request->link_page;
        }

        $menu = new Menu();
        $menu->nama_menu = $request->nama_menu;
        $menu->jenis_menu = $request->jenis_menu;
        $menu->url_menu = $url_menu;
        $menu->target_menu = $request->target_menu;
        $menu->urutan_menu = $urut;
        $menu->parent_menu = $request->parent_menu;

        try {
            $menu->save();
            return redirect(route('menu.index'))->with('pesan', ['success', 'Berhasil tambah menu']);
        } catch (\Exception $e) {
            return redirect(route('menu.index'))->with('pesan', ['danger', 'Gagal tambah menu']);
        }
    }


    public function hapus($id){
        $menu = Menu::findOrFail($id);

        try {
            $menu->delete();
            return redirect(route('menu.index'))->with('pesan', ['success', 'Berhasil hapus sub menu']);
        } catch (\Exception $e) {
            return redirect(route('menu.index'))->with('pesan', ['danger', 'Gagal hapus sub menu']);
        }

    }

    public function ubah($id){
        $page = Page::where('status_page', 1)->get();
        $parent = Menu::whereNull('parent_menu')->where('status_menu', 1)->get();
        $menu = Menu::findOrFail($id);
        return view('backend.content.menu.formUbah', compact('page', 'parent', 'menu'));
    }

    public function prosesUbah(Request $request){
        $this->validate($request, [
            'id_menu'=> 'required',
            'nama_menu' => 'required',
            'jenis_menu' => 'required',
            'target_menu' => 'required',
        ]);

        $url_menu = "";
        if($request->jenis_menu == "url" ){
            $url_menu = $request->link_url;
        }else{
            $url_menu = $request->link_page;
        }

        $menu = Menu::findOrFail($request->id_menu);
        $menu->nama_menu = $request->nama_menu;
        $menu->jenis_menu = $request->jenis_menu;
        $menu->url_menu = $url_menu;
        $menu->target_menu = $request->target_menu;
        $menu->parent_menu = $request->parent_menu;
        $menu->status_menu = $request->status_menu;

        try {
            $menu->save();
            return redirect(route('menu.index'))->with('pesan', ['success', 'Berhasil ubah menu']);
        } catch (\Exception $e) {
            return redirect(route('menu.index'))->with('pesan', ['danger', 'Gagal ubah menu']);
        }
    }

    private function getUrutanMenu($parent = null)
    {
        if ($parent === null) {
            // Menu
            $urut = Menu::select('urutan_menu')->whereNull('parent_menu')->orderBy('urutan_menu', 'desc')->first();
            return $urut ? $urut->urutan_menu + 1 : 1;
        } else {
            // Submenu
            $urutSubMenu = Menu::select('urutan_menu')->where('parent_menu', $parent)->orderBy('urutan_menu', 'desc')->first();
            return $urutSubMenu ? $urutSubMenu->urutan_menu + 1 : 1;
        }
    }
}
