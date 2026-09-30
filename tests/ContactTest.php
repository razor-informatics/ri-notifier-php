<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use RazorInformatics\RiNotifierPhp\Constants;
use RazorInformatics\RiNotifierPhp\Contact;

class ContactTest extends TestCase
{
    public function testListsAPageOfContacts(): void
    {
        $contacts = new Contact($this->client($this->json([
            'data' => [['id' => 'c1', 'phone' => '254700100100', 'labels' => []]],
            'links' => ['next' => null],
            'meta' => ['current_page' => 2, 'per_page' => 50],
        ])));

        $result = $contacts->list(2, 50);

        $this->assertRequest('GET', 'contacts', 'page=2&per_page=50');
        $this->assertSame(Constants::STATUS_SUCCESS, $result['status']);
        $this->assertSame('c1', $result['data'][0]->id);
        $this->assertNull($result['links']->next);
        $this->assertSame(2, $result['meta']->current_page);
    }

    public function testListDefaultsToTheFirstPage(): void
    {
        (new Contact($this->client($this->json(['data' => []]))))->list();

        $this->assertRequest('GET', 'contacts', 'page=1&per_page=15');
    }

    public function testCreatesAContact(): void
    {
        $contacts = new Contact($this->client($this->json(['data' => ['id' => 'c1']], 201)));

        $result = $contacts->create([
            'phone' => '0700100100',
            'name' => 'Jane Wanjiku',
            'labels' => ['L1'],
            'id' => 'ignored',
        ]);

        $this->assertRequest('POST', 'contacts');
        $this->assertSame(['phone' => '0700100100', 'name' => 'Jane Wanjiku', 'labels' => ['L1']], $this->jsonBody());
        $this->assertSame('c1', $result['data']->id);
    }

    public function testCreateNeedsAPhone(): void
    {
        $result = (new Contact($this->client()))->create(['name' => 'Jane']);

        $this->assertNoRequest();
        $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => 'phone must be defined.', 'data' => []], $result);
    }

    public function testCreateReturnsValidationErrors(): void
    {
        $contacts = new Contact($this->client($this->json([
            'message' => 'The phone has already been taken.',
            'errors' => ['phone' => ['The phone has already been taken.']],
        ], 422)));

        $result = $contacts->create(['phone' => '0700100100']);

        $this->assertSame('The phone has already been taken.', $result['message']);
        $this->assertSame(['phone' => ['The phone has already been taken.']], $result['data']);
    }

    public function testGetsAContact(): void
    {
        $result = (new Contact($this->client($this->json(['data' => ['id' => 'c 1']]))))->get('c 1');

        $this->assertRequest('GET', 'contacts/c%201');
        $this->assertSame('c 1', $result['data']->id);
    }

    public function testUpdatesOnlyTheGivenFields(): void
    {
        $contacts = new Contact($this->client($this->json(['data' => ['id' => 'c1']])));

        $contacts->update('c1', ['name' => null, 'labels' => [], 'other' => 'x']);

        $this->assertRequest('PUT', 'contacts/c1');
        $this->assertSame(['name' => null, 'labels' => []], $this->jsonBody());
    }

    public function testAnUpdateWithoutFieldsSendsAnEmptyObject(): void
    {
        (new Contact($this->client($this->json(['data' => []]))))->update('c1');

        $this->assertSame('{}', (string) $this->lastRequest()->getBody());
    }

    public function testDeletesAContact(): void
    {
        $contacts = new Contact($this->client(new \GuzzleHttp\Psr7\Response(204)));

        $result = $contacts->delete('c1');

        $this->assertRequest('DELETE', 'contacts/c1');
        $this->assertSame(['status' => Constants::STATUS_SUCCESS, 'data' => []], $result);
    }

    public function testAMissingContactIsNotFound(): void
    {
        $contacts = new Contact($this->client($this->json(['message' => 'Contact not found.'], 404)));

        $result = $contacts->get('nope');

        $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => 'Contact not found.', 'data' => []], $result);
    }

    public function testAnEmptyContactIdIsRejected(): void
    {
        $contacts = new Contact($this->client());

        foreach ([$contacts->get(''), $contacts->update(''), $contacts->delete('')] as $result) {
            $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => 'contact id must be defined.', 'data' => []], $result);
        }
        $this->assertNoRequest();
    }
}
