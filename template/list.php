<table id="remind-me-table" class="widefat">
	<thead>
		<tr>
			<th>Related Post Title</th>
			<th>Last Modified</th>
			<th>Apply Link</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Related Post Title</th>
			<th>Last Modified</th>
			<th>Apply Link</th>
		</tr>
	</tfoot>
	<tbody>
<?php foreach ($rows as $row) : ?>
		<tr>
			<td><strong><?php echo $row->title; ?></strong></td>
			<td><?php echo $row->date; ?></td>
			<td><a href="<?php echo get_permalink($row->ID); ?>" class="remind-me-link" rel="<?php echo $row->count ?>">[ Add Link ]</a></td>
		</tr>	
<?php endforeach ?>
	</tbody>
</table>