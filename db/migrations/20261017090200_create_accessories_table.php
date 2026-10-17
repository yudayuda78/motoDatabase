<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAccessoriesTable extends AbstractMigration
{
	public function change(): void
	{
		$this->table('accessories')
			->addColumn('nama', 'string', ['limit' => 120])
			->addColumn('kategori', 'string', ['limit' => 60])
			->addColumn('harga', 'biginteger')
			->addColumn('ikon', 'string', ['limit' => 30])
			->addIndex(['kategori'], ['name' => 'accessories_kategori_index'])
			->addTimestamps()
			->create();
	}
}
