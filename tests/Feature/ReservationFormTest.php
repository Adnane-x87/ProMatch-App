<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReservationFormTest extends TestCase
{
    public function test_booking_form_sends_field_id_from_selected_terrain(): void
    {
        $this->get('/booking')
            ->assertOk()
            ->assertSee('name="field_id"', false)
            ->assertSee('x-ref="fieldSelect"', false)
            ->assertSee('x-model="formData.field_id"', false)
            ->assertSee(':value="fieldId(field)"', false)
            ->assertSee('field?.id ?? field?.field_id ?? field?.terrain_id ?? field?.terrainId', false)
            ->assertSee("console.log('SELECTED FIELD BEFORE SEND'", false)
            ->assertSee("console.log('FORMDATA ITEM:'", false)
            ->assertSee('selectedFieldIdForSubmit()', false)
            ->assertSee("body.append('field_id', String(fieldId))", false);
    }
}
