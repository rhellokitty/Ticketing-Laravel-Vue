<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        try {

            $query = Ticket::query();

            $query->orderBy('created_at', 'desc');

            if ($request->search) {
                $query->where('code', 'Like', '%' . $request->search . '%')
                    ->orWhere('title', 'Like', '%' . $request->search . '%');
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->priority) {
                $query->where('priority', $request->priority);
            }

            if (auth()->user()->role == 'user') {
                $query->where('user_id', auth()->user()->id);
            }

            $tickets = $query->get();

            return response()->json([
                'message' => 'Data Berhasil Diambil',
                'data' => TicketResource::collection($tickets)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => null
            ], 500);
        }
    }


    public function store(TicketStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $ticket = new Ticket();
            $ticket->user_id = auth()->user()->id;
            $ticket->code = 'TIC' . rand(1000, 9999);
            $ticket->title = $data['title'];
            $ticket->description = $data['description'];
            $ticket->priority = $data['priority'];
            $ticket->save();

            DB::commit();

            return response()->json([
                'message' => 'Ticket Berhasil Dibuat',
                'data' => new TicketResource($ticket)
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $ticket = Ticket::findOrFail($id);

            if (auth()->user()->role == 'user' && $ticket->user_id != auth()->user()->id) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses untuk menghapus ticket ini'
                ], 403);
            }

            $ticket->delete();

            DB::commit();

            return response()->json([
                'message' => 'Ticket Berhasil Dihapus'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => null
            ], 500);
        }
    }

    // LANJUTKAN PADA MENIT 1:16:00

}
