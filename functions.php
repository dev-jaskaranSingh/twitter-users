<?php
/**
 * Created by PhpStorm.
 * User: mbasr
 * Date: 27-Oct-16
 * Time: 3:43 AM
 */
require_once 'Request.php';

$functions = new Functions();

$method = $_GET['method'];
if (!method_exists($functions, $method)) {
    return die('The function you are looking for must be missing');
}

return $functions->$method();

class Functions {

    public function search() {

        $location = (isset($_POST['location']) && !empty($_POST['location'])) ? $_POST['location'] : 'Pakistan';
        $from = (isset($_POST['from']) && !empty($_POST['from'])) ? $_POST['from'] : '10k';
        $to = (isset($_POST['to']) && !empty($_POST['to']) ? $_POST['to'] : '100k');

        $from = (1000* (int) str_replace(['k', 'K'], ['', ''], $from));
        $to = (1000* (int) str_replace(['k', 'K'], ['', ''], $to));

        if (!is_numeric($from) || !is_numeric($to)) {
            return $this->jsonFailedNoRecord('Invalid value in "To" or "From" field');
        }

        $users = (new Request($location, $from, $to))->search();
        if (count($users) < 1) {
            return $this->jsonFailedNoRecord();
        }
        return $this->jsonSuccessResponse($users);
    }

    private function jsonSuccessResponse($message) {
        header('Content-Type: application/json');
        header('charset: utf-8');
        header("HTTP/1.0 200 OK");
        echo json_encode($message);
    }

    private function jsonFailedNoRecord($message = "no record found") {
        header('Content-Type: application/json');
        header('charset: utf-8');
        header("HTTP/1.0 204 No Content");
        echo json_encode($message);
    }
}