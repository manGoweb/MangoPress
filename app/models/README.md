Models
======

<dl>
	<dt>`factories`</dt>
		<dd>Create new services.</dd>
	<dt>`orm`</dt>
		<dd>Low level stuff related to ORM and Abstract repository/mapper/entity.</dd>
    <dt>`rme`</dt>
		<dd>Repositories, mappers and entities. Each triad should be in a separate folder named with plural of the original entity name.</dd>
	<dt>`services`</dt>
		<dd>Bread and butter</dd>
	<dt>`structs`</dt>
        <dd>Classes for structuring data. Example: `LatLng` for storing latitude and longitude.</dd>
    <dt>`tasks`</dt>
        <dd>Tasks that can be `Queue::enqueue`<i>d</i>. All tasks must be descendants of `App\Models\Tasks\Task`.</dd>
</dl>
