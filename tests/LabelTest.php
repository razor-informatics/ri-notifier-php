<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\TestWith;
use RazorInformatics\RiNotifierPhp\Constants;
use RazorInformatics\RiNotifierPhp\Label;

class LabelTest extends TestCase
{
    public function testListsAPageOfLabels(): void
    {
        $labels = new Label($this->client($this->json([
            'data' => [['id' => 'l1', 'name' => 'VIP', 'contacts_count' => 3]],
            'links' => ['next' => 'https://notifier.test/api/labels?page=2'],
            'meta' => ['total' => 20],
        ])));

        $result = $labels->list(1, 1);

        $this->assertRequest('GET', 'labels', 'page=1&per_page=1');
        $this->assertSame(3, $result['data'][0]->contacts_count);
        $this->assertSame('https://notifier.test/api/labels?page=2', $result['links']->next);
        $this->assertSame(20, $result['meta']->total);
    }

    public function testListOmitsMissingPagination(): void
    {
        $result = (new Label($this->client($this->json(['data' => []]))))->list();

        $this->assertSame(['status' => Constants::STATUS_SUCCESS, 'data' => []], $result);
    }

    public function testCreatesALabel(): void
    {
        $result = (new Label($this->client($this->json(['data' => ['id' => 'l1', 'name' => 'VIP']], 201))))->create('VIP');

        $this->assertRequest('POST', 'labels');
        $this->assertSame(['name' => 'VIP'], $this->jsonBody());
        $this->assertSame('l1', $result['data']->id);
    }

    #[TestWith([''])]
    #[TestWith(['   '])]
    public function testCreateNeedsAName(string $name): void
    {
        $result = (new Label($this->client()))->create($name);

        $this->assertNoRequest();
        $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => 'name must be defined.', 'data' => []], $result);
    }

    public function testGetsALabel(): void
    {
        (new Label($this->client($this->json(['data' => ['id' => 'l/1']]))))->get('l/1');

        $this->assertRequest('GET', 'labels/l%2F1');
    }

    #[TestWith(['update'])]
    #[TestWith(['rename'])]
    public function testRenamesALabel(string $method): void
    {
        $result = (new Label($this->client($this->json(['data' => ['name' => 'Gold']]))))->{$method}('l1', 'Gold');

        $this->assertRequest('PUT', 'labels/l1');
        $this->assertSame(['name' => 'Gold'], $this->jsonBody());
        $this->assertSame('Gold', $result['data']->name);
    }

    #[TestWith(['', 'Gold'])]
    #[TestWith(['l1', ' '])]
    public function testRenameNeedsAnIdAndName(string $id, string $name): void
    {
        $result = (new Label($this->client()))->rename($id, $name);

        $this->assertNoRequest();
        $this->assertSame('label id and name must be defined.', $result['message']);
    }

    public function testDeletesALabel(): void
    {
        $result = (new Label($this->client(new Response(204))))->delete('l1');

        $this->assertRequest('DELETE', 'labels/l1');
        $this->assertSame(['status' => Constants::STATUS_SUCCESS, 'data' => []], $result);
    }

    public function testAnEmptyLabelIdIsRejected(): void
    {
        $labels = new Label($this->client());

        foreach ([$labels->get(''), $labels->delete('')] as $result) {
            $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => 'label id must be defined.', 'data' => []], $result);
        }
        $this->assertNoRequest();
    }
}
