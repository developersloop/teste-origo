<?php

namespace App\Http\Repositories;

use App\Links;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

define("SHORTENED_LINK", true);
define("DISABLED", false);

class LinksRepository {

    public function links() {
        $links = Links::all() ?? [];
        return $this->response(200, '', $links);
    }

    public function link($id = null, $link = '',  $short_link = '') {
        return  DB::table('links as lk')
            ->where(function($query) use ($id, $link, $short_link) {
                $query->where('id', $id)
                    ->orWhere('current_link', $link)
                    ->orWhere('short_link', $short_link);
            });
    }

    public function CastShortLink($link) {
        $expire_link = $this->link('',$link)->count() > 0
            ? $this->link('',$link)->first()->link_expirate
            : Carbon::now();

        $diffExpirateData = Carbon::parse(Carbon::now())->diffInDays($expire_link);
        $existsLink = ($this->link('',$link)->exists() && $diffExpirateData <= 7);

        if($existsLink) return $this->response(404, 'Não é possível encurtar um link já existente.');

        else {
            $url = $link;
            if (!str_contains($url, 'https')) return $this->response(500, 'Url Invalida');

            $headers = get_headers($url, 1);
            $isInValidUrlRedirect = str_contains($headers[0], 'Not Found');

            if ($isInValidUrlRedirect) return $this->response(404, 'Link nao e valido para ser redirecionado');

            else {
                $hash_link = Hash::make($link);

                Links::create([
                    'current_link'   => $link ,
                    'short_link'     => $hash_link,
                    'shortened_link' => SHORTENED_LINK,
                    'disabled'       => DISABLED,
                    'link_expirate'  => Carbon::now()->addDays(7),
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now()
                ]);

                return redirect()->away($link);
            }
        }

    }

    public function deleteLink($id = null, $link = '',  $short_link = '') {

        $link = $this->link($id, $link, $short_link);

        if ($link->count() > 0) {

            $isShortLink = $link->first()->shortened_link == 1
                ? true
                : false;

            if (!$isShortLink) {
                $link->delete();
                return $this->response(202,'Link excluido com sucesso.');
            }

            else return $this->response(404,'Nao é possível excluir o link.');
        }

        else  return $this->response(404,'Nao existe este link.');
    }

    public function updateLink($id = null, $link = '',  $short_link = '', $newLink) {

        $link = $this->link($id, $link, $short_link);

        if ($link->count() > 0) {

            $isShortLink = $link->first()->shortened_link == 1
                ? true
                : false;

            if (!$isShortLink) {

                $url = $newLink;

                if (!str_contains($url, 'https')) return $this->response(500, 'Url Invalida');

                $headers = get_headers($url, 1);
                $isInValidUrlRedirect = str_contains($headers[0], 'Not Found');

                if ($isInValidUrlRedirect) return $this->response(404, 'Link nao e valido para ser redirecionado');

                $link->update([
                    'current_link'   => $newLink,
                    'short_link'     => Hash::make($newLink),
                    'updated_at'     => Carbon::now()
                ]);

                return $this->response(200,'Link editado com sucesso.');
            }

            else return  $this->response(404,'Nao é possível alterar o link.');

        }

        else return  $this->response(404,'Nao existe este link.');
    }

    public function disableLink($id) {
        $link = $this->link($id);

        if ($link->count() > 0) {
            Links::find($id)->update(['disabled' => true]);
            return $this->response(200, 'Link disabilitado com sucesso.');
        }
        else  return  $this->response(404,'Nao existe este link.');
    }

    public function response($status, $message, $data = []) {

        return response()->json([
                'status'  => $status,
                'data'    => $data ?? [],
                'message' => $message,
            ],
        $status);
    }

    public function disableExpirateForTestCypress($id) {
        $link = $this->link($id);

        if ($link->count() > 0) {
            $link->update([
                'shortened_link' => false
            ]);

            return $this->response(200, 'Link habilitado para edição ou remoção');
        }

        else return  $this->response(404,'Nao existe este link.');
    }
}

?>
