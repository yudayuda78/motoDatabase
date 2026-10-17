<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBrandsTable extends AbstractMigration
{
	public function change(): void
	{
		$this->table('brands')
			->addColumn('nama', 'string', ['limit' => 60])
			->addColumn('negara', 'string', ['limit' => 60, 'null' => true])
			->addIndex(['nama'], ['unique' => true, 'name' => 'brands_nama_unique'])
			->addTimestamps()
			->create();
	}
}
