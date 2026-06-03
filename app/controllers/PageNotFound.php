<?php
class PageNotFound extends BaseController {
    public function index() {
        http_response_code(404);
        $this->view('errors/404');
    }
}