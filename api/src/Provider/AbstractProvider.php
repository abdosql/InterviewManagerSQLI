<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider;

use Doctrine\ODM\MongoDB\DocumentManager;

readonly abstract class AbstractProvider
{
    public function __construct(protected DocumentManager $documentManager)
    {
    }
}