<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\GravityForms\Hooks;

use GF_Field;

class FormSubmission
{
    /**
     * @param array{id: int} $entry
     * @param array{id: int, title: string, fields: array} $form
     */
    public function __invoke(array $entry, array $form)
    {
    }
}