<?php

class Term_Entry_Type extends Papi_Entry_Type {

	/**
	 * Define our Entry Type meta data.
	 *
	 * @return array
	 */
	public function meta() {
		return [
			'name'        => 'Term test entry 2',
			'description' => 'Entry type to test out the term property'
		];
	}
}
