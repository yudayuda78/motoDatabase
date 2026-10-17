<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMotorsTable extends AbstractMigration
{
	public function change(): void
	{
		$this->table('motors')
			->addColumn('nama', 'string', ['limit' => 120])
			->addColumn('merek', 'string', ['limit' => 60])
			->addColumn('model', 'string', ['limit' => 80])
			->addColumn('tipe', 'string', ['limit' => 60])
			->addColumn('harga', 'biginteger')
			->addColumn('featured', 'boolean', ['default' => false])
			->addIndex(['merek'], ['name' => 'motors_merek_index'])
			->addIndex(['featured'], ['name' => 'motors_featured_index'])
			->addTimestamps()
			->create();
	}
}
