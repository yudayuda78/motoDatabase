<?php declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class AccessorySeeder extends AbstractSeed
{
	public function run(): void
	{
		$existing = $this->fetchRow('SELECT COUNT(*) AS jumlah FROM accessories');
		if ((int) ($existing['jumlah'] ?? 0) > 0) {
			return;
		}

		$this->table('accessories')
			->insert([
				['nama' => 'Ban Tubeless', 'kategori' => 'Ban & Velg', 'harga' => 385000, 'ikon' => 'ban'],
				['nama' => 'Velg Racing', 'kategori' => 'Ban & Velg', 'harga' => 1250000, 'ikon' => 'velg'],
				['nama' => 'Knalpot Racing', 'kategori' => 'Performa', 'harga' => 950000, 'ikon' => 'knalpot'],
				['nama' => 'Cakram Rem', 'kategori' => 'Pengereman', 'harga' => 420000, 'ikon' => 'rem'],
			])
			->saveData();
	}
}
