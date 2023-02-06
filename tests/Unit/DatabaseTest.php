<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if status database has expected columns.
     *
     */
    public function test_status_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('status', [
                'status_id', 'comment', 'updated_at', 'created_at'
            ]), 1);
    }

    /**
     * Check if owners database has expected columns.
     *
     */
    public function test_owners_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('owners', [
                'owner_id', 'siren', 'kbis', 'status_id'
            ]), 1);
    }

    /**
     * Check if employees database has expected columns.
     *
     */
    public function test_employees_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('employees', [
                'employee_id', 'hiring_date', 'dismissal_date'
            ]), 1);
    }

    /**
     * Check if barathoniens database has expected columns.
     *
     */
    public function test_barathoniens_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('barathoniens', [
                'barathonien_id', 'birthday', 'address_id'
            ]), 1);
    }

    /**
     * Check if administrators database has expected columns.
     *
     */
    public function test_administrators_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('administrators', [
                'administrator_id', 'superAdmin'
            ]), 1);
    }

    /**
     * Check if users database has expected columns.
     *
     */
    public function test_users_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'user_id', 'first_name', 'last_name', 'email', 'email_verified_at', 'password', 'avatar', 'owner_id', 'barathonien_id', 'administrator_id', 'employee_id', 'remember_token', 'deleted_at', 'updated_at', 'created_at'
            ]), 1);
    }

    /**
     * Check if establishments database has expected columns.
     *
     */
    public function test_establishments_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('establishments', [
                'establishment_id', 'trade_name', 'siret', 'address_id', 'logo', 'phone', 'email', 'website', 'opening', 'owner_id', 'status_id', 'deleted_at', 'updated_at', 'created_at'
            ]), 1);
    }

    /**
     * Check if establishments_employees database has expected columns.
     *
     */
    public function test_establishments_employees_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('establishments_employees', [
                'establishment_employee_id', 'establishment_id', 'employee_id', 'updated_at', 'created_at'
            ]), 1);
    }

    /**
     * Check if categories database has expected columns.
     *
     */
    public function test_categories_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('categories', [
                'category_id', 'label'
            ]), 1);
    }

    /**
     * Check if events database has expected columns.
     *
     */
    public function test_events_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('events', [
                'event_id', 'event_name', 'description', 'start_event', 'end_event', 'poster', 'price', 'capacity', 'establishment_id', 'status_id', 'user_id', 'deleted_at', 'updated_at', 'created_at'
            ]), 1);
    }


}
