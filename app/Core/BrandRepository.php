<?php declare(strict_types=1);

namespace App\Core;

use Nette;

final class BrandRepository
{
	public function __construct(
		private readonly Nette\Database\Explorer $db,
	) {
	}

	/** @return array<int, array<string, mixed>> */
	public function findAll(): array
	{
		$result = [];
		foreach ($this->db->table('brands')->order('nama')->fetchAll() as $row) {
			$result[] = $row->toArray();
		}

		return $result;
	}

	/** @return ?array<string, mixed> */
	public function find(int $id): ?array
	{
		$row = $this->db->table('brands')->get($id);

		return $row === null ? null : $row->toArray();
	}
}
