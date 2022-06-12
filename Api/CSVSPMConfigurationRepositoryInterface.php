<?php

namespace WMZ\CSVSPM\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationInterface;
use WMZ\CSVSPM\Api\Data\CSVSPMConfigurationResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;

interface CSVSPMConfigurationRepositoryInterface
{
    /**
     * Save
     * @param CSVSPMConfigurationInterface $CSVSPMConfiguration
     * @return CSVSPMConfigurationInterface
     * @throws CouldNotSaveException
     */
    public function save(CSVSPMConfigurationInterface $CSVSPMConfiguration);

    /**
     * Get by id
     * @param int $id
     * @return CSVSPMConfigurationInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     * @param CSVSPMConfigurationInterface $CSVSPMConfiguration
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(CSVSPMConfigurationInterface $CSVSPMConfiguration);

    /**
     * Delete by id
     * @param int $id
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     * @param SearchCriteriaInterface $searchCriteria
     * @return CSVSPMConfigurationResultsInterface
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
