<?php

namespace App\Http\Controllers;

use App\Http\Repositories\LinksRepository;
use App\Http\Requests\LinkRequest;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    private $serviceLink;

    public function __construct(LinksRepository $serviceLink)
    {
      $this->serviceLink =  $serviceLink;
    }

    public function links() {
        try {
           return $this->serviceLink->links();

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' =>  $e->getMessage(),
            ],500);
        }
    }

    public function disable($id) {
        try {
            return $this->serviceLink->disableLink($id);

        } catch (\Exception $e) {
            return response()->json([
                    'status' => 500,
                    'message' =>  $e->getMessage(),
                ],
            500);
        }
    }

    public function shortLink(LinkRequest $request) {
        try {

            if($request->validated()) {
                $link = $request->validated()['link'];
                return $this->serviceLink->CastShortLink($link);
            }


        } catch (\Exception $e) {
            return response()->json([
                    'status' => 500,
                    'message' =>  $e->getMessage(),
                ],
            500);
        }
    }


    public function updateLink(LinkRequest $request, $id = null, $link = null, $short_link = null) {
        try {

            if($request->validated()) {
                $link = $request->validated()['link'];
                return $this->serviceLink->updateLink($id, $link, $short_link, $link);
            }

        } catch (\Exception $e) {
            return response()->json([
                    'status' => 500,
                    'message' =>  $e->getMessage(),
                ],
            500);
        }
    }
    public function deleteLInk($id = null, $link = null, $short_link = null) {
        try {

           return $this->serviceLink->deleteLInk($id, $link, $short_link);

        } catch (\Exception $e) {
            return response()->json([
                    'status' => 500,
                    'message' =>  $e->getMessage(),
                ],
            500);
        }
    }
}
