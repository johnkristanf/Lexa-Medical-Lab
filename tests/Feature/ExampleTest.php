<?php

it('test login page render', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});
