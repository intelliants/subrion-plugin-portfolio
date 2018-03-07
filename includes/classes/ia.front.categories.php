<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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

class iaCategories extends iaAbstractFrontHelperCategoryFlat
{
    protected static $_table = 'portfolio_categs';

    protected $_itemName = 'portfolio_categ';

    protected $_moduleName = 'portfolio';

    protected $_patterns = [
        'view' => 'portfolio/:title_alias'
    ];


    public function url($action, array $data)
    {
        empty($data['title_alias']) || $data['title_alias'].= IA_URL_DELIMITER;

        return IA_URL . iaDb::printf($this->_patterns[$action], $data);
    }
}
