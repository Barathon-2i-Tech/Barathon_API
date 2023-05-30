<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if status database has expected columns.
     */
    public function test_status_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('status', [
                'status_id', 'comment', 'updated_at', 'created_at'
            ]));
    }

    /**
     * Check if owners database has expected columns.
     */
    public function test_owners_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('owners', [
                'owner_id', 'siren', 'kbis', 'phone', 'company_name', 'status_id'
            ]));
    }

    /**
     * Check if employees database has expected columns.
     */
    public function test_employees_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('employees', [
                'employee_id', 'hiring_date', 'dismissal_date'
            ]));
    }

    /**
     * Check if barathoniens database has expected columns.
     */
    public function test_barathoniens_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('barathoniens', [
                'barathonien_id', 'birthday', 'address_id'
            ]));
    }

    /**
     * Check if administrators database has expected columns.
     */
    public function test_administrators_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('administrators', [
                'administrator_id', 'superAdmin'
            ]));
    }

    /**
     * Check if users database has expected columns.
     */
    public function test_users_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'user_id', 'first_name', 'last_name', 'email', 'email_verified_at', 'password', 'avatar', 'owner_id', 'barathonien_id', 'administrator_id', 'employee_id', 'remember_token', 'deleted_at', 'updated_at', 'created_at'
            ]));
    }

    /**
     * Check if establishments database has expected columns.
     */
    public function test_establishments_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('establishments', [
                'establishment_id', 'trade_name', 'siret', 'address_id', 'logo', 'phone', 'email', 'website', 'opening', 'owner_id', 'status_id', 'deleted_at', 'updated_at', 'created_at'
            ]));
    }

    /**
     * Check if establishments_employees database has expected columns.
     */
    public function test_establishments_employees_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('establishments_employees', [
                'establishment_employee_id', 'establishment_id', 'employee_id', 'updated_at', 'created_at'
            ]));
    }

    /**
     * Check if categories database has expected columns.
     */
    public function test_categories_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('categories', [
                'category_id', 'category_details'
            ]));
    }

    /**
     * Check if events database has expected columns.
     */
    public function test_events_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('events', [
                'event_id', 'event_name', 'description', 'start_event', 'end_event', 'poster', 'price', 'capacity', 'establishment_id', 'status_id', 'user_id', 'deleted_at', 'event_update_id', 'updated_at', 'created_at'
            ]));
    }

    /**
     * Check if bookings database has expected columns.
     */
    public function test_bookings_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('bookings', [
                'booking_id', 'user_id', 'event_id', 'ticket'
            ]));
    }

    /**
     * Check if addresses database has expected columns.
     */
    public function test_addresses_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('addresses', [
                'address_id', 'address', 'postal_code', 'city'
            ]));
    }

    /**
     * Check if categories_establishments database has expected columns.
     */
    public function test_categories_establishments_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('categories_establishments', [
                'category_establishment_id', 'category_id', 'establishment_id', 'created_at', 'updated_at'
            ]));
    }

    /**
     * Check if categories_events database has expected columns.
     */
    public function test_categories_events_database_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('categories_events', [
                'category_event_id', 'category_id', 'event_id', 'created_at', 'updated_at'
            ]));
    }
}
