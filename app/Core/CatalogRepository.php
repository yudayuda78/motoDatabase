<?php declare(strict_types=1);

namespace App\Core;

use Nette;

final class CatalogRepository
{
	public function __construct(
		private readonly Nette\Database\Explorer $db,
	) {
	}

	/** @return array<int, array<string, mixed>> */
	public function findAll(): array
	{
		return $this->toArrays(
			$this->db->table('motors')->order('id')->fetchAll(),
		);
	}

	/** @return ?array<string, mixed> */
	public function find(int $id): ?array
	{
		$row = $this->db->table('motors')->get($id);

		return $row === null ? null : $row->toArray();
	}

	/** @return ?array<string, mixed> */
	public function findFeatured(): ?array
	{
		$row = $this->db->table('motors')->where('featured', true)->order('id')->fetch()
			?? $this->db->table('motors')->order('id')->fetch();

		return $row === null ? null : $row->toArray();
	}

	/**
	 * @param  iterable<Nette\Database\Row> $rows
	 * @return array<int, array<string, mixed>>
	 */
	private function toArrays(iterable $rows): array
	{
		$result = [];
		foreach ($rows as $row) {
			$result[] = $row->toArray();
		}

		return $result;
	}
}
