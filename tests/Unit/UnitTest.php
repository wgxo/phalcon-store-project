<?php

declare(strict_types=1);

namespace Tests\Unit;

class UnitTest extends AbstractUnitTest
{
	public function testTestCase(): void
	{
		self::assertEquals(
			"roman",
			"roman",
			"This will pass"
		);

		self::assertEquals(
			"hope",
			"ava",
			"This will fail"
		);
	}
}
