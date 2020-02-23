<?php

namespace App\models;

use App\Config\Config;
use DateTime;
use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use Ramsey\Uuid\Uuid;

class EET
{
    /**
     * Generating EET receipt
     * @param $data
     * @return array
     * @throws \Exception
     */
    public static function prepare($data)
    {
        $receipt = new Receipt();
        $receipt->uuid_zpravy = Uuid::uuid4()->toString();
        $receipt->id_provoz = Config::getSetting("id_provoz");
        $receipt->id_pokl = Config::getSetting("id_pokl");
        $receipt->porad_cis = '141-18543-05';
        $receipt->dic_popl = "CZ" . Config::getSetting("dic_obchodnika");
        $receipt->dat_trzby = new DateTime;
        $receipt->celk_trzba = floatval($data["total"]);

        $certificate = new Certificate(Config::$EETCert["path"], Config::$EETCert["password"]);
        $dispatcher = new Dispatcher($certificate, Dispatcher::PLAYGROUND_SERVICE);

        $returnAttr = [
            "dat_trzby" => $receipt->dat_trzby,
            "FIK" => "",
            "BKP" => "",
            "PKP" => ""
        ];

        try {
            $dispatcher->send($receipt);
            $returnAttr["state"] = "send";
            $returnAttr["FIK"] = $dispatcher->getFik();
            $returnAttr["BKP"] = $dispatcher->getBkp();
        } catch (FilipSedivy\EET\Exceptions\EET\ErrorException $exception) {
            $returnAttr["state"] = "mustBeResend";
            $returnAttr["PKP"] = $exception->getPkp();
            $returnAttr["BKP"] = $dispatcher->getBkp();
        } catch (FilipSedivy\EET\Exceptions\EET\ErrorException $exception) {
            $returnAttr["state"] = "error";
            $returnAttr["error"] = '(' . $exception->getCode() . ') ' . $exception->getMessage();
        } catch (FilipSedivy\EET\Exceptions\Receipt\ConstraintViolationException $violationException) {
            $returnAttr["state"] = "errorViolation";
            $returnAttr["error"] = implode('<br>', $violationException->getErrors());
        }


        return $returnAttr;
    }
}
