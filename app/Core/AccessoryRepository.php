<?php declare(strict_types=1);

namespace App\Core;

use Nette;

final class AccessoryRepository
{
	public function __construct(
		private readonly Nette\Database\Explorer $db,
	) {
	}

	/** @return array<int, array<string, mixed>> */
	public function findAll(): array
	{
		$result = [];
		foreach ($this->db->table('accessories')->order('id')->fetchAll() as $row) {
			$result[] = $row->toArray();
		}

		return $result;
	}
}
