<?php

/**
 * VariableHelper.php
 *
 * Part of the Trait-Libraray for IP-Symcon Modules.
 *
 * @package       traits
 * @author        Heiko Wilknitz <heiko@wilkware.de>
 * @copyright     2021 Heiko Wilknitz
 * @link          https://wilkware.de
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

declare(strict_types=1);

/**
 * Helper class for access satus variables.
 */
trait VariableHelper
{
    /**
     * Update a boolean value.
     *
     * @param string $ident Ident of the boolean variable
     * @param bool   $value Value of the boolean variable
     */
    protected function SetValueBoolean(string $ident, bool $value)
    {
        $id = $this->GetIDForIdent($ident);
        if ($id !== false) {
            SetValueBoolean($id, $value);
        }
    }

    /**
     * Update a string value.
     *
     * @param string $ident Ident of the string variable
     * @param string $value Value of the string variable
     */
    protected function SetValueString(string $ident, string $value)
    {
        $id = $this->GetIDForIdent($ident);
        if ($id !== false) {
            SetValueString($id, $value);
        }
    }

    /**
     * Update a integer value.
     *
     * @param string $ident Ident of the integer variable
     * @param int    $value Value of the integer variable
     */
    protected function SetValueInteger(string $ident, int $value)
    {
        $id = $this->GetIDForIdent($ident);
        if ($id !== false) {
            SetValueInteger($id, $value);
        }
    }

    /**
     * Update a float value.
     *
     * @param string $ident Ident of the float variable
     * @param float  $value Value of the float variable
     */
    protected function SetValueFloat(string $ident, float $value)
    {
        $id = $this->GetIDForIdent($ident);
        if ($id !== false) {
            SetValueFloat($id, $value);
        }
    }
}
