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

require_once IA_INCLUDES . 'phpimageworkshop' . IA_DS . 'ImageWorkshop.php';
use phpimageworkshop\ImageWorkshop as ImageWorkshop;

class iaImage extends iaPicture
{
	public function processFolioImage($aFile, $folder, $aName, $imageInfo)
	{
		$ext = self::_getImageExt($aFile['type']);

		if (empty($ext))
		{
			$this->setMessage(iaLanguage::getf('file_type_error', array('extension' => implode(', ', array_unique(self::$_typesMap)))));

			return false;
		}

		$path = IA_UPLOADS . $folder;
		$image = ImageWorkshop::initFromPath($aFile['tmp_name']);

		// save source image
		$image->save($path, self::SOURCE_PREFIX . $aName . $ext);

		// process thumbnails for files uploaded in CKEditor and other tools
		if (empty($imageInfo))
		{
			// apply watermark
			$image = self::_applyWaterMark($image);
			$image->save($path, self::_createFilename($aName, $ext));

			return true;
		}

		// check this is an animated GIF
		if ('image/gif' == $aFile['type'] && $this->iaCore->get('allow_animated_gifs'))
		{
			require_once IA_INCLUDES . 'phpimageworkshop' . IA_DS . 'Core' . IA_DS . 'GifFrameExtractor.php';

			$gifPath = $aFile['tmp_name'];
			if (GifFrameExtractor::isAnimatedGif($gifPath))
			{
				// Extractions of the GIF frames and their durations
				$gfe = new GifFrameExtractor();
				$frames = $gfe->extract($gifPath);

				// For each frame, we add a watermark and we resize it
				$retouchedFrames = array();
				$thumbFrames = array();
				foreach ($frames as $frame)
				{
					$frameLayer = ImageWorkshop::initFromResourceVar($frame['image']);
					$thumbLayer = ImageWorkshop::initFromResourceVar($frame['image']);

					$frameLayer->resizeInPixel($imageInfo['image_width'], $imageInfo['image_height'], true);
					$frameLayer = self::_applyWaterMark($frameLayer);
					$retouchedFrames[] = $frameLayer->getResult();

					$thumbLayer->resizeInPixel($imageInfo['thumb_width'], $imageInfo['thumb_height'], true);
					$thumbFrames[] = $thumbLayer->getResult();
				}

				// Then we re-generate the GIF
				require_once IA_INCLUDES . 'phpimageworkshop' . IA_DS . 'Core' . IA_DS . 'GifCreator.php';

				$gc = new GifCreator();
				$gc->create($retouchedFrames, $gfe->getFrameDurations(), 0);
				file_put_contents($path . self::_createFilename($aName, $ext), $gc->getGif());

				$thumbCreator = new GifCreator();
				$thumbCreator->create($thumbFrames, $gfe->getFrameDurations(), 0);
				file_put_contents($path . self::_createFilename($aName, $ext, true), $thumbCreator->getGif());

				return self::_createFilename($folder . $aName, $ext, true);
			}
		}

		// save full image
		$largestSide = ($imageInfo['image_width'] > $imageInfo['image_height']) ? $imageInfo['image_width'] : $imageInfo['image_height'];

		if ($largestSide)
		{
			$image->resizeByLargestSideInPixel($largestSide, true);
		}

		$image = self::_applyWaterMark($image);
		$image->save($path, self::_createFilename($aName, $ext));

		// generate thumbnail
		$thumbWidth = $imageInfo['thumb_width'] ? $imageInfo['thumb_width'] : $this->iaCore->get('thumb_w');
		$thumbHeight = $imageInfo['thumb_height'] ? $imageInfo['thumb_height'] : $this->iaCore->get('thumb_h');
		$positionX = $imageInfo['positionX'] ? $imageInfo['positionX'] : 0;
		$positionY = $imageInfo['positionY'] ? $imageInfo['positionY'] : 0;
		$position = $imageInfo['position'] ? $imageInfo['position'] : 'MM';
		$resize = $imageInfo['resize'] ? $imageInfo['resize'] : 'before_crop';

		$cropWidth = $imageInfo['crop_width'] ? $imageInfo['crop_width'] : '';
		$cropHeight = $imageInfo['crop_height'] ? $imageInfo['crop_height'] : '';

		if ($thumbWidth || $thumbHeight)
		{
			$thumb = ImageWorkshop::initFromPath($aFile['tmp_name']);
			switch ($imageInfo['resize_mode'])
			{
				case self::FIT:
					$thumb->resizeInPixel($thumbWidth, $thumbHeight, true, 0, 0, 'MM');

					break;

				case self::CROP:
					$largestSide = $thumbWidth > $thumbHeight ? $thumbWidth : $thumbHeight;

					if ($this->iaCore->get('portfolio_use_crop'))
					{
						if ('after_crop' == $resize)
						{
							// $thumb->cropMaximumInPixel(0, 0, 'MM');
							$thumb->cropInPixel($cropWidth, $cropHeight, $positionX, $positionY, $position);
							$thumb->resizeInPixel($thumbWidth, $thumbHeight);
						}
						else 
						{
							$thumb->cropMaximumInPixel(0, 0, 'MM');
							$thumb->resizeInPixel($largestSide, $largestSide);
							$thumb->cropInPixel($thumbWidth, $thumbHeight, $positionX, $positionY, $position);
						}
					}
					else
					{
						$thumb->cropMaximumInPixel(0, 0, 'MM');
						$thumb->resizeInPixel($largestSide, $largestSide);
						$thumb->cropInPixel($thumbWidth, $thumbHeight, 0, 0, 'MM');
					}
			}

			$thumb->save($path, self::_createFilename($aName, $ext, true));
		}

		return self::_createFilename($folder . $aName, $ext, true);
	}
}