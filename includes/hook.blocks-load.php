<?php

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if ($iaView->blockExists('portfolio_tags'))
	{
		$sql =
			'SELECT DISTINCT SQL_CALC_FOUND_ROWS pt.`id`, pt.`title`, pt.`alias` ' .
			'FROM `:prefix:table_portfolio_tags` pt ' .
			'LEFT JOIN `:prefix:table_portfolio_entries_tags` pet ON (pt.`id` = pet.`tag_id`) ' .
			'LEFT JOIN `:prefix:table_portfolio_entries` p ON (p.`id` = pet.`portfolio_id`) ' .
			'WHERE p.`status` = \':status\' ' .
			'GROUP BY pt.`id` ' .
			'ORDER BY pt.`title` ' .
			'LIMIT :start, :limit';

		$sql = iaDb::printf($sql, array(
			'prefix' => $iaDb->prefix,
			'table_portfolio_entries' => 'portfolio_entries',
			'table_portfolio_entries_tags' => 'portfolio_entries_tags',
			'table_portfolio_tags' => 'portfolio_tags',
			'status' => iaCore::STATUS_ACTIVE,
			'start' => 0,
			'limit' => $iaCore->get('tag_number', 10)
		));
		$tags = $iaDb->getAll($sql);

		$iaView->assign('block_portfolio_tags', $tags);
	}

	if ($iaView->blockExists('new_portfolio_entries'))
	{
		$iaPortfolio = $iaCore->factoryPlugin('portfolio', iaCore::FRONT, 'portfolio');

		$stmt = '`status` = :status AND `lang` = :language ORDER BY `date_added` DESC';
		$iaDb->bind($stmt, array('status' => iaCore::STATUS_ACTIVE, 'language' => $iaView->language));

		$entries = $iaDb->all(array('id', 'title', 'date_added', 'alias', 'body', 'image'), $stmt, 0, $iaCore->get('portfolio_block_count'), 'portfolio_entries');

		$iaView->assign('tags', $iaPortfolio->getAllTags());
		$iaView->assign('block_portfolio_entries', $entries);
	}
}