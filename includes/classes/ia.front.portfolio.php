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

class iaPortfolio extends abstractPlugin
{
	const ALIAS_SUFFIX = '.html';

	const PAGE_NAME = 'portfolio';

	protected static $_table = 'portfolio_entries';
	protected $_tablePortfolioTags = 'portfolio_tags';
	protected $_tablePortfolioEntriesTags = 'portfolio_entries_tags';

	public function getTags($portfolioEntryId)
	{
		$sql =
			'SELECT DISTINCT pt.`title`, pt.`alias` ' .
			'FROM `:prefix:table_portfolio_tags` pt ' .
			'LEFT JOIN `:prefix:table_portfolio_entries_tags` pet ON (pt.`id` = pet.`tag_id`) ' .
			'WHERE pet.`portfolio_id` = :id';

		$sql = iaDb::printf($sql, array(
			'prefix' => $this->iaDb->prefix,
			'table_portfolio_entries_tags' => $this->_tablePortfolioEntriesTags,
			'table_portfolio_tags' => $this->_tablePortfolioTags,
			'id' => (int)$portfolioEntryId
		));

		return $this->iaDb->getAll($sql);
	}

	public function getAllTags()
	{
		$sql =
			'SELECT pt.`title`, pt.`alias`, pet.`portfolio_id` ' .
			'FROM `:prefix:table_portfolio_tags` pt ' .
			'LEFT JOIN `:prefix:table_portfolio_entries_tags` pet ON (pt.`id` = pet.`tag_id`) ' .
			'ORDER BY pt.`title`';

		$sql = iaDb::printf($sql, array(
			'prefix' => $this->iaDb->prefix,
			'table_portfolio_entries_tags' => $this->_tablePortfolioEntriesTags,
			'table_portfolio_tags' => $this->_tablePortfolioTags
		));

		return $this->iaDb->getAll($sql);
	}
}