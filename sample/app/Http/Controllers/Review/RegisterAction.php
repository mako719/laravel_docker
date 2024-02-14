<?php

namespace App\Http\Controllers\Review;

use App\DataProvider\RegisterReviewProviderInterface;
use App\Events\ReviewRegistered;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class RegisterAction extends Controller
{
    private $provider;

    private $dispatcher;

    // データベース登録とEvent発火を行うクラスのインスタンスが渡される
    public function __construct(
        RegisterReviewProviderInterface $provider,
        Dispatcher $dispatcher
    )
    {
        $this->provider = $provider;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(Request $request): Response
    {
        $created = Carbon::now()->toDateTimeString();
        // 登録処理の実行
        $id = $this->provider->save(
            $request->get('title'),
            $request->get('content'),
            $request->get('user_id', 1),
            $created,
            $request->get('tags')
        );

        // 登録後にイベント発火
        $this->dispatcher->dispatch(
            new ReviewRegistered(
                $id,
                $request->get('title'),
                $request->get('content'),
                $request->get('user_id', 1),
                $created,
                $request->get('tags')
            )
        );
        // POSTで動作するため、登録完了後HTTP Statusのみ返却
        return new Response('', Response::HTTP_OK);
    }
}
