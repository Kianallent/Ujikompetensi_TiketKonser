<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with(['kategori', 'tikets'])
            ->when($request->kategori_id, function ($query) use ($request) {
                $query->where('kategori_id', $request->kategori_id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('lokasi', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy(
                'tanggal_waktu',
                $request->get('sort', 'asc')
            )
            ->paginate(10);

        $kategoris = Kategori::all();

        return view(
            'pages.admin.events.index',
            compact(
                'events',
                'kategoris'
            )
        );
    }

    public function create()
    {
        $kategoris = Kategori::all();

        return view(
            'pages.admin.events.create',
            compact('kategoris')
        );
    }

    public function store(EventFormRequest $request)
    {
        $gambar = 'konser.jpg';

        if ($request->hasFile('gambar')) {
            $gambar = $request
                ->file('gambar')
                ->store('events', 'public');
        }

        $event = Event::create([
            'user_id' => Auth::id(),
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'gambar' => $gambar,
            'tanggal_waktu' => $request->tanggal_waktu,
        ]);

        foreach ($request->tikets as $tiket) {
            $event->tikets()->create([
                'tipe' => $tiket['tipe'],
                'harga' => $tiket['harga'],
                'stok' => $tiket['stok'],
            ]);
        }

        return redirect()
            ->route('admin.events.index')
            ->with(
                'success',
                'Event berhasil dibuat'
            );
    }

    public function edit(Event $event)
    {
        $event->load('tikets');
        $kategoris = Kategori::all();

        return view(
            'pages.admin.events.edit',
            [
                'event' => $event,
                'kategoris' => $kategoris,
                'hasSales' => $event->hasSales(),
            ]
        );
    }

    public function update(
        EventFormRequest $request,
        Event $event
    ) {
        if (
            $event->hasSales() &&
            $request->tanggal_waktu != $event->tanggal_waktu
        ) {
            return back()
                ->withErrors(
                    'Tanggal event tidak dapat diubah karena sudah ada penjualan'
                );
        }

        $data = [
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'tanggal_waktu' => $request->tanggal_waktu,
        ];

        if ($request->hasFile('gambar')) {
            if (
                $event->gambar != 'konser.jpg'
                &&
                Storage::disk('public')->exists($event->gambar)
            ) {
                Storage::disk('public')
                    ->delete($event->gambar);
            }

            $data['gambar'] =
                $request
                ->file('gambar')
                ->store('events', 'public');
        }

        $event->update($data);

        // Menyimpan ID tiket yang masih ada di form
        $ticketIds = [];

        foreach ($request->tikets as $tiket) {

            if (isset($tiket['id']) && ! empty($tiket['id'])) {

                $ticketIds[] = $tiket['id'];

                $event->tikets()
                    ->where('id', $tiket['id'])
                    ->update([
                        'tipe' => $tiket['tipe'],
                        'harga' => $tiket['harga'],
                        'stok' => $tiket['stok'],
                    ]);
            } else {

                $newTicket = $event->tikets()->create([
                    'tipe' => $tiket['tipe'],
                    'harga' => $tiket['harga'],
                    'stok' => $tiket['stok'],
                ]);

                $ticketIds[] = $newTicket->id;
            }
        }

        // Hapus tiket yang tidak ada lagi di form
        if (! $event->hasSales()) {

            $event->tikets()
                ->whereNotIn('id', $ticketIds)
                ->delete();
        }

        return redirect()
            ->route('admin.events.index')
            ->with(
                'success',
                'Event berhasil diperbarui'
            );
    }

    public function destroy(Event $event)
    {
        if ($event->hasSales()) {

            return back()
                ->withErrors(
                    'Event tidak dapat dihapus karena sudah memiliki penjualan'
                );
        }

        if (
            $event->gambar != 'konser.jpg'
            &&
            Storage::disk('public')
            ->exists($event->gambar)
        ) {

            Storage::disk('public')
                ->delete($event->gambar);
        }

        $event->delete();

        return back()
            ->with(
                'success',
                'Event berhasil dihapus'
            );
    }

    public function show(Event $event)
    {
        $event->load([
            'kategori',
            'tikets',
        ]);

        $relatedEvents = Event::where(
            'kategori_id',
            $event->kategori_id
        )
            ->where(
                'tanggal_waktu',
                '>',
                now()
            )
            ->where(
                'id',
                '!=',
                $event->id
            )
            ->limit(4)
            ->get();

        return view(
            'events.show',
            compact(
                'event',
                'relatedEvents'
            )
        );
    }
}
