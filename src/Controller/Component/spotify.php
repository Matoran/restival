<?php
/**
 * Created by PhpStorm.
 * User: matoran
 * Date: 11/15/17
 * Time: 11:16 AM
 */
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;

class MathComponent extends Component
{
    public function getArtisteFromName($name)
    {
        $client = new Client();
        $client->get();
        return $name;
    }
}