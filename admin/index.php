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

class iaBackendController extends iaAbstractControllerModuleBackend
{
	protected $_name = 'portfolio';
	protected $_table = 'portfolio_entries';
	protected $_tablePortfolioTags = 'portfolio_tags';
	protected $_tablePortfolioEntriesTags = 'portfolio_entries_tags';

	protected $_pluginName = 'portfolio';

	protected $_gridColumns = array('title', 'alias', 'date_added', 'status');
	protected $_gridFilters = array('status' => 'equal');

	protected $_phraseAddSuccess = 'pf_added';
	protected $_phraseGridEntryDeleted = 'pf_deleted';


	public function __construct()
	{
		parent::__construct();

		$iaPortfolio = $this->_iaCore->factoryPlugin($this->getModuleName(), iaCore::ADMIN, $this->getName());
		$this->setHelper($iaPortfolio);
	}

	protected function _modifyGridParams(&$conditions, &$values)
	{
		if (!empty($_GET['text']))
		{
			$conditions[] = '(`title` LIKE :text OR `body` LIKE :text)';
			$values['text'] = '%' . iaSanitize::sql($_GET['text']) . '%';
		}
	}

	protected function _gridRead($params)
	{
		return (isset($params['get']) && 'alias' == $params['get'])
			? array('url' => IA_URL . 'portfolio' . IA_URL_DELIMITER . $this->_iaDb->getNextId() . '-' . $this->getHelper()->titleAlias($params['title']))
			: parent::_gridRead($params);
	}

	protected function _setPageTitle(&$iaView)
	{
		if (in_array($iaView->get('action'), array(iaCore::ACTION_ADD, iaCore::ACTION_EDIT)))
		{
			$iaView->title(iaLanguage::get('pf_' . $iaView->get('action')));
		}
	}

	protected function _setDefaultValues(array &$entry)
	{
		$entry['title'] = $entry['body'] = $entry['tags'] = '';
		$entry['lang'] = $this->_iaCore->iaView->language;
		$entry['date_added'] = date(iaDb::DATETIME_FORMAT);
		$entry['status'] = iaCore::STATUS_ACTIVE;
	}

	protected function _entryDelete($entryId)
	{
		return (bool)$this->getHelper()->delete($entryId);
	}

	protected function _preSaveEntry(array &$entry, array $data, $action)
	{
		parent::_preSaveEntry($entry, $data, $action);

		iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

		if (!utf8_is_valid($entry['title']))
		{
			$entry['title'] = utf8_bad_replace($entry['title']);
		}

		if (empty($entry['title']))
		{
			$this->addMessage('title_is_empty');
		}

		if (!utf8_is_valid($entry['body']))
		{
			$entry['body'] = utf8_bad_replace($entry['body']);
		}

		if (empty($entry['body']))
		{
			$this->addMessage(iaLanguage::getf('field_is_empty', array('field' => iaLanguage::get('description'))), false);
		}

		if (empty($entry['date_added']))
		{
			$entry['date_added'] = date(iaDb::DATETIME_FORMAT);
		}

		$entry['alias'] = $this->getHelper()->titleAlias(empty($entry['alias']) ? $entry['title'] : $entry['alias']);

		if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'])
		{
			$this->_iaCore->loadClass(iaCore::CORE, 'picture');

			$iaImage = $this->_iaCore->factoryPlugin($this->getModuleName(), iaCore::ADMIN, 'image');

			$imageData = json_decode($entry['image-data'], true);
			$path = iaUtil::getAccountDir();
			$file = $_FILES['image'];
			$token = iaUtil::generateToken();
			$info = array(
				'image_width' => $this->_iaCore->get('portfolio_image_width'),
				'image_height' => $this->_iaCore->get('portfolio_image_height'),
				'crop_width' => $imageData['width'],
				'crop_height' => $imageData['height'],
				'thumb_width' => $this->_iaCore->get('portfolio_thumbnail_width'),
				'thumb_height' => $this->_iaCore->get('portfolio_thumbnail_height'),
				'positionX' => $imageData['x'],
				'positionY' => $imageData['y'],
				'position' => 'LT',
				'resize' => 'after_crop',
				'resize_mode' => iaImage::CROP
			);

			if ($image = $iaImage->processFolioImage($file, $path, $token, $info))
			{
				if ($entry['image']) // it has an already assigned image
				{
					$iaImage = $this->_iaCore->factory('picture');
					$iaImage->delete($entry['image']);
				}

				$entry['image'] = $image;
			}
		}

		if (empty($entry['image']))
		{
			$this->addMessage('invalid_image_file');
		}

		if ($this->getMessages())
		{
			return false;
		}

		unset($entry['image-src'], $entry['image-data'], $entry['tags']);

		return true;
	}

	protected function _postSaveEntry(array &$entry, array $data, $action)
	{
		$iaLog = $this->_iaCore->factory('log');

		$actionCode = (iaCore::ACTION_ADD == $action)
			? iaLog::ACTION_CREATE
			: iaLog::ACTION_UPDATE;
		$params = array(
			'module' => 'portfolio',
			'item' => 'portfolio',
			'name' => $entry['title'],
			'id' => $this->getEntryId()
		);

		$iaLog->write($actionCode, $params);

		$this->_saveTags($data['tags']);
	}

	protected function _saveTags($tagsString)
	{
		$tags = array_filter(explode(',', $tagsString));

		$this->_iaDb->setTable($this->_tablePortfolioEntriesTags);

		$sql ='DELETE ' .
			'FROM `:prefix:table_portfolio_tags` ' .
			'WHERE `id` IN (' .
			'SELECT DISTINCT `tag_id` ' .
			'FROM `:prefix:table_portfolio_entries_tags` ' .
			'WHERE `tag_id` IN (' .
			'SELECT DISTINCT `tag_id` FROM `:prefix:table_portfolio_entries_tags` ' .
			'WHERE `portfolio_id` = :id) ' .
			'GROUP BY 1 ' .
			'HAVING COUNT(*) = 1)';

		$sql = iaDb::printf($sql, array(
			'prefix' => $this->_iaDb->prefix,
			'table_portfolio_tags' => $this->_tablePortfolioTags,
			'table_portfolio_entries_tags' => $this->_tablePortfolioEntriesTags,
			'id' => $this->getEntryId()
		));

		$this->_iaDb->query($sql);
		$sql =
			'DELETE ' .
			'FROM :prefix:table_portfolio_entries_tags ' .
			'WHERE `portfolio_id` = :id';
		$sql = iaDb::printf($sql, array(
			'prefix' => $this->_iaDb->prefix,
			'table_portfolio_entries_tags' => $this->_tablePortfolioEntriesTags,
			'id' => $this->getEntryId()
		));

		$this->_iaDb->query($sql);

		$allTagTitles = $this->_iaDb->keyvalue(array('title','id'), null, $this->_tablePortfolioTags);

		foreach ($tags as $tag)
		{
			$tagAlias = iaSanitize::alias(strtolower($tag));
			$tagEntry = array(
				'title' => $tag,
				'alias' => $tagAlias
			);
			$tagId = isset($allTagTitles[$tag])
				? $allTagTitles[$tag]
				: $this->_iaDb->insert($tagEntry, null, $this->_tablePortfolioTags);

			$tagPortfolioIds = array(
				'portfolio_id' => $this->getEntryId(),
				'tag_id' => $tagId
			);

			$this->_iaDb->insert($tagPortfolioIds);
		}
	}

	protected function _assignValues(&$iaView, array &$entryData)
	{
		if ($this->getEntryId())
		{
			$entryData['tags'] = $this->getHelper()->getTags($this->getEntryId());
		}
		else if (isset($_POST['tags']))
		{
			$entryData['tags'] = iaSanitize::sql($_POST['tags']);
		}
	}
}