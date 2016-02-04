<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2016 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (isset($iaCore->requestPath[0]))
	{
		$tag = $iaCore->requestPath[0];

		$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
		$page = ($page < 1) ? 1 : $page;

		$pageUrl = $iaCore->factory('page', iaCore::FRONT)->getUrlByName('portfolio_tag');

		$pagination = array(
			'start' => ($page - 1) * $iaCore->get('portfolio_entries_per_page'),
			'limit' => (int)$iaCore->get('portfolio_entries_per_page'),
			'template' => $pageUrl . '?page={page}'
		);

		$sql =
			'SELECT SQL_CALC_FOUND_ROWS ' .
			'p.`id`, p.`title`, p.`date_added`, p.`body`, p.`alias`, p.`image`, pt.`title` `tag_title`' .
			'FROM `:prefix:table_portfolio_entries` p ' .
			'LEFT JOIN `:prefix:table_portfolio_entries_tags` pet ON (p.`id` = pet.`portfolio_id`) ' .
			'LEFT JOIN `:prefix:table_portfolio_tags` pt ON (pt.`id` = pet.`tag_id`) ' .
			'WHERE pt.`alias` = \':tag\' AND pet.`tag_id` = pt.`id` ' .
			'AND p.`status` = \':status\' LIMIT :start, :limit';

		$sql = iaDb::printf($sql, array(
			'prefix' => $iaDb->prefix,
			'table_portfolio_entries' => 'portfolio_entries',
			'table_portfolio_entries_tags' => 'portfolio_entries_tags',
			'table_portfolio_tags' => 'portfolio_tags',
			'tag' => iaSanitize::sql($tag),
			'status' => iaCore::STATUS_ACTIVE,
			'start' => $pagination['start'],
			'limit' => $pagination['limit']
		));

		$portfolioEntries = $iaDb->getAll($sql);

		$pagination['total'] = $iaDb->foundRows();

		if (empty($portfolioEntries)) {
			return iaView::errorPage(iaView::ERROR_NOT_FOUND);
		}
		$title = '#' . $portfolioEntries[0]['tag_title'];
		iaBreadcrumb::toEnd($title);

		$iaView->title($title);

		$iaView->display('tag');

		$iaView->assign('pagination', $pagination);
		$iaView->assign('portfolio_entries', $portfolioEntries);
	}

	else {
		$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
		$page = ($page < 1) ? 1 : $page;

		$pageUrl = $iaCore->factory('page', iaCore::FRONT)->getUrlByName('portfolio_tag');

		$pagination = array(
			'start' => ($page - 1) * $iaCore->get('tag_number'),
			'limit' => (int)$iaCore->get('tag_number'),
			'template' => $pageUrl . '?page={page}'
		);

		$prefix = $iaDb->prefix;
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
			'start' => $pagination['start'],
			'limit' => $pagination['limit']
		));

		$tags = $iaDb->getAll($sql);
		$pagination['total'] = $iaDb->foundRows();

		$iaView->assign('tags', $tags);
		$iaView->assign('pagination', $pagination);
		$iaView->display('tag');
	}
}
