<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Berita; // Mengimport model Berita
use App\Models\Page;

class HomeController extends Controller
{
    public function index()
    {
        #halaman awal
        $menu = $this->getMenu();
        $berita = Berita::latest()->get()->take(6);
        $mostViews = Berita::orderByDesc('total_views')->take(3)->get();
        return view('frontend.content.home', compact('menu', 'berita', 'mostViews'));
    }


    public function detailBerita($id)
    {
        // halaman detail berita
        $menu = $this->getMenu();
        $berita = Berita::findOrFail($id);

        #update total_view
        $berita->total_views = $berita->total_views + 1;
        $berita->save();
         return view('frontend.content.detailBerita', compact('menu', 'berita'));

    }

    public function detailPage($id)
    {
        // Detail halaman
        $menu = $this->getMenu();
        $page = Page::findOrFail($id);
        return view('frontend.content.detailPage', compact('menu', 'page'));

    }

    public function semuaBerita()
    {
        // Semua berita
        $menu = $this->getMenu();
        $berita = Berita::with('kategori')->latest()->get();
        return view('frontend.content.semuaBerita', compact('menu', 'berita'));

    }

    private function getMenu()
    {
        $menu = Menu::whereNull('parent_menu')
            ->with(['submenu' => fn ($q) => $q->where('status_menu', '=', 1)->orderBy('urutan_menu', 'asc')])
            ->where('status_menu', '=', 1)
            ->orderBy('urutan_menu', 'asc')
            ->get();

        $dataMenu = [];
        foreach ($menu as $m) {
            $jenis_menu = $m->jenis_menu;
            $urlMenu = "";

            if ($jenis_menu == "url") {
                $urlMenu = $m->url_menu;
            } else {
                $urlMenu = route('home.detailPage', $m->url_menu);
            }

            // Item Menu
            $dItemMenu = [];
            foreach ($m->submenu as $im) {
                $jenisItemMenu = $im->jenis_menu;
                $urlItemMenu = "";

                if ($jenisItemMenu == "url") {
                    $urlItemMenu = $im->url_menu;
                } else {
                    $urlMenu = route('home.detailPage', $im->url_menu);
                }

                $dItemMenu[] = [
                    'sub_menu_nama' => $im->nama_menu,
                    'sub_menu_target' => $im->target_menu,
                    'sub_menu_url' => $urlItemMenu,
                ];
            }

            $dataMenu[] = [
                'menu' => $m->nama_menu,
                'target' => $m->target_menu,
                'url' => $urlMenu,
                'itemMenu' => $dItemMenu,
            ];
        }
        return $dataMenu;
    }
}

