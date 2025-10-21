// @ts-check
const { test, expect } = require('@playwright/test');

const baseURL = process.env.BASE_URL || 'https://localhost';

test('swagger', async ({ page }) => {
  await page.goto(`${baseURL}/docs`);
  await expect(page).toHaveTitle('Hello API Platform - API Platform');
  await expect(page.locator('.operation-tag-content > span')).toHaveCount(5);
});

test('admin', async ({ page, browserName }) => {
  await page.goto(`${baseURL}/admin`);
  await page.getByLabel('Create').click();
  await page.getByLabel('Name').fill('foo' + browserName);
  await page.getByLabel('Save').click();
  await expect(page).toHaveURL(/admin#\/greetings$/);
  await page.getByText('foo' + browserName).first().click();
  await expect(page).toHaveURL(/show$/);
  await page.getByLabel('Edit').first().click();
  await page.getByLabel('Name').fill('bar' + browserName);
  await page.getByLabel('Save').click();
  await expect(page).toHaveURL(/admin#\/greetings$/);
  await page.getByText('bar' + browserName).first().click();
});
