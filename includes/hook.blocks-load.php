<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2019 Intelliants, LLC <https://intelliants.com>
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
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    if ($iaView->blockExists('top_portfolio_categories')) {
        $iaCategory = $iaCore->factoryModule('categories', 'portfolio');
        $topCategories = $iaCategory->getByLevel(1);

        $iaView->assign('topPortfolioCategories', $topCategories);
    }

    if ($iaView->blockExists('new_portfolio_entries')) {
        $iaPortfolio = $iaCore->factoryModule('portfolio', 'portfolio');

        $entries = $iaPortfolio->getAll(iaDb::EMPTY_CONDITION, iaDb::ALL_COLUMNS_SELECTION, 0, $iaCore->get('portfolio_block_count'));
        $categories = [];

        foreach ($entries as $entry) {
            $categories[$entry['category_id']] = $entry['category_title'];
        }

        $iaView->assign('newPortfolioEntries', $entries);
        $iaView->assign('newPortfolioEntriesCategories', $categories);
    }
}