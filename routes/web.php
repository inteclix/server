<?php

$router->get('/{any:.*}', function () {
    return view('main');
});