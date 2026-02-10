# MedEquip Platform - End-to-End Testing Checklist

## 📋 Overview

This document provides a comprehensive testing checklist for the MedEquip B2B Medical Marketplace platform, covering both **Buyer** and **Supplier** workflows.

---

## 🛒 BUYER WORKFLOW TESTING

### 1. Registration & Authentication

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 1.1 | Buyer Registration | Navigate to `/register/buyer`, fill all required fields, submit | Account created, redirect to waiting approval page | ⬜ |
| 1.2 | Required Fields Validation | Submit registration form with empty fields | Arabic validation messages shown | ⬜ |
| 1.3 | Email Uniqueness | Register with existing email | "البريد الإلكتروني مستخدم بالفعل" error | ⬜ |
| 1.4 | Login (Unverified) | Login with unverified buyer account | Redirect to waiting approval page | ⬜ |
| 1.5 | Login (Verified) | Login with verified buyer account | Redirect to buyer dashboard | ⬜ |
| 1.6 | Logout | Click logout | Session ended, redirect to login | ⬜ |

### 2. Dashboard

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 2.1 | Dashboard Loads | Navigate to `/buyer/dashboard` | Dashboard displays with stats & charts | ⬜ |
| 2.2 | Stats Accuracy | Check stats cards | Numbers match database records | ⬜ |
| 2.3 | Charts Render | Check ApexCharts | Charts display correctly with data | ⬜ |
| 2.4 | Quick Actions | Click quick action buttons | Navigate to correct pages | ⬜ |
| 2.5 | Recent Activity | Check recent orders section | Shows latest 5 orders | ⬜ |

### 3. Profile Management

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 3.1 | View Profile | Navigate to `/buyer/profile` | Profile details displayed | ⬜ |
| 3.2 | Edit Profile | Click edit, modify fields, save | Changes saved successfully | ⬜ |
| 3.3 | Upload Documents | Upload license documents | Documents stored via Spatie Media | ⬜ |
| 3.4 | Validation | Submit invalid data | Arabic validation errors shown | ⬜ |

### 4. Product Catalog

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 4.1 | Browse Products | Navigate to `/buyer/products` | Grid of approved products shown | ⬜ |
| 4.2 | Search Products | Enter search term | Filtered results displayed | ⬜ |
| 4.3 | Filter by Category | Select category | Products filtered by category | ⬜ |
| 4.4 | Filter by Manufacturer | Select manufacturer | Products filtered by manufacturer | ⬜ |
| 4.5 | Sort Products | Change sort option | Products reordered correctly | ⬜ |
| 4.6 | Grid/List Toggle | Toggle view mode | View switches between grid/list | ⬜ |
| 4.7 | Product Details | Click on product | Product details page loads | ⬜ |
| 4.8 | Add to Favorites | Click heart icon | Product added to favorites | ⬜ |
| 4.9 | Add to Compare | Click compare button | Product added to comparison | ⬜ |

### 5. Shopping Cart / RFQ Builder

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 5.1 | Add to Cart | Click "Add to Cart" on product | Product added, cart count updated | ⬜ |
| 5.2 | View Cart | Navigate to `/buyer/cart` | Cart contents displayed | ⬜ |
| 5.3 | Update Quantity | Change item quantity | Quantity updated in session | ⬜ |
| 5.4 | Remove Item | Click remove button | Item removed from cart | ⬜ |
| 5.5 | Clear Cart | Click clear all | All items removed | ⬜ |
| 5.6 | Checkout | Click proceed to checkout | Checkout form displayed | ⬜ |
| 5.7 | Submit RFQ from Cart | Fill form, submit | RFQ created with cart items | ⬜ |
| 5.8 | Cart Persistence | Add items, logout, login | Cart items preserved in session | ⬜ |

### 6. RFQ Management

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 6.1 | View RFQs | Navigate to `/buyer/rfqs` | List of buyer's RFQs | ⬜ |
| 6.2 | Create RFQ | Click create, fill form, submit | New RFQ created | ⬜ |
| 6.3 | Add RFQ Items | Add multiple items to RFQ | Items added with quantities | ⬜ |
| 6.4 | Edit Draft RFQ | Edit RFQ in draft status | Changes saved | ⬜ |
| 6.5 | Submit RFQ | Change status to open | RFQ open for quotations | ⬜ |
| 6.6 | View RFQ Details | Click on RFQ | Details page with items shown | ⬜ |
| 6.7 | Cancel RFQ | Click cancel button | RFQ cancelled | ⬜ |
| 6.8 | Deadline Validation | Set past deadline | Validation error shown | ⬜ |

### 7. Quotation Evaluation

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 7.1 | View Quotations | Navigate to `/buyer/quotations` | List of received quotations | ⬜ |
| 7.2 | Filter by Status | Select status filter | Quotations filtered | ⬜ |
| 7.3 | View Quotation Details | Click on quotation | Details with items displayed | ⬜ |
| 7.4 | Compare Quotations | Select multiple, click compare | Comparison table displayed | ⬜ |
| 7.5 | Accept Quotation | Click accept button | Quotation accepted, order created | ⬜ |
| 7.6 | Reject Quotation | Click reject button | Quotation rejected | ⬜ |

### 8. Orders

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 8.1 | View Orders | Navigate to `/buyer/orders` | List of orders displayed | ⬜ |
| 8.2 | Order Details | Click on order | Order details with timeline | ⬜ |
| 8.3 | Order Timeline | Check status timeline | Visual timeline shows progress | ⬜ |
| 8.4 | Track Delivery | Check delivery section | Delivery status displayed | ⬜ |

### 9. Supplier Directory

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 9.1 | Browse Suppliers | Navigate to `/buyer/suppliers` | List of verified suppliers | ⬜ |
| 9.2 | Search Suppliers | Enter search term | Filtered suppliers shown | ⬜ |
| 9.3 | View Supplier | Click on supplier | Supplier profile displayed | ⬜ |
| 9.4 | View Supplier Products | Check products tab | Supplier's products listed | ⬜ |
| 9.5 | View Supplier Rating | Check rating section | Average rating displayed | ⬜ |

### 10. Reports & Analytics

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 10.1 | View Reports | Navigate to `/buyer/reports` | Reports dashboard displayed | ⬜ |
| 10.2 | Spending Trends | Check spending chart | Chart renders with data | ⬜ |
| 10.3 | Top Suppliers | Check top suppliers section | List of suppliers by spending | ⬜ |
| 10.4 | Date Filter | Change date range | Charts update accordingly | ⬜ |

---

## 🏭 SUPPLIER WORKFLOW TESTING

### 1. Registration & Authentication

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 1.1 | Supplier Registration | Navigate to `/register/supplier`, fill form | Account created, redirect to approval | ⬜ |
| 1.2 | Login (Unverified) | Login with unverified account | Redirect to waiting approval | ⬜ |
| 1.3 | Login (Verified) | Login with verified account | Redirect to supplier dashboard | ⬜ |

### 2. Dashboard

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 2.1 | Dashboard Loads | Navigate to `/supplier/dashboard` | Dashboard with stats & charts | ⬜ |
| 2.2 | Revenue Chart | Check revenue chart | 6-month revenue trend displayed | ⬜ |
| 2.3 | Order Status Chart | Check orders donut | Order distribution shown | ⬜ |
| 2.4 | Product Review Chart | Check products donut | Review status distribution | ⬜ |

### 3. Product Management

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 3.1 | View Products | Navigate to `/supplier/products` | List of supplier's products | ⬜ |
| 3.2 | Add Product | Click create, fill form, submit | Product created in pending status | ⬜ |
| 3.3 | Upload Images | Upload product images | Images stored via Spatie Media | ⬜ |
| 3.4 | Edit Product | Edit product details | Changes saved, status reset to pending | ⬜ |
| 3.5 | Set Price & Stock | Update pricing information | Pivot data updated | ⬜ |
| 3.6 | Remove Product | Click remove button | Product removed from supplier list | ⬜ |
| 3.7 | Filter Products | Use filter options | Products filtered accordingly | ⬜ |

### 4. RFQ Response

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 4.1 | View Available RFQs | Navigate to `/supplier/rfqs` | List of assigned/public RFQs | ⬜ |
| 4.2 | View RFQ Details | Click on RFQ | RFQ details with items shown | ⬜ |
| 4.3 | Submit Quotation | Fill quotation form, submit | Quotation created | ⬜ |
| 4.4 | Price Per Item | Enter price for each item | Item prices calculated correctly | ⬜ |
| 4.5 | Total Price Validation | Submit mismatched total | Validation error shown | ⬜ |
| 4.6 | Deadline Check | Try to quote expired RFQ | Error message shown | ⬜ |

### 5. Quotation Management

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 5.1 | View Quotations | Navigate to `/supplier/quotations` | List of submitted quotations | ⬜ |
| 5.2 | Edit Pending Quote | Edit quotation in pending status | Changes saved | ⬜ |
| 5.3 | Delete Quote | Delete pending quotation | Quotation removed | ⬜ |
| 5.4 | Export Quotations | Click export button | Excel file downloaded | ⬜ |

### 6. Order Fulfillment

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| 6.1 | View Orders | Navigate to `/supplier/orders` | List of supplier's orders | ⬜ |
| 6.2 | Update Order Status | Change order status | Status updated, buyer notified | ⬜ |
| 6.3 | Create Delivery | Create delivery record | Delivery record created | ⬜ |
| 6.4 | Upload Proof | Upload delivery proof | Document stored | ⬜ |

---

## 🔧 CROSS-CUTTING CONCERNS

### Authorization & Security

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| X.1 | Buyer Access Only | Supplier tries buyer route | 403 Forbidden | ⬜ |
| X.2 | Supplier Access Only | Buyer tries supplier route | 403 Forbidden | ⬜ |
| X.3 | Own Resources Only | User A tries to access User B's data | 403 Forbidden | ⬜ |
| X.4 | Verified Only | Unverified user tries protected route | Redirect to approval page | ⬜ |

### Notifications

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| N.1 | New RFQ Notification | Buyer creates public RFQ | Suppliers receive notification | ⬜ |
| N.2 | New Quotation Notification | Supplier submits quote | Buyer receives notification | ⬜ |
| N.3 | Order Status Notification | Order status changes | Relevant party notified | ⬜ |

### Performance

| # | Test Case | Steps | Expected Result | Status |
|---|-----------|-------|-----------------|--------|
| P.1 | Dashboard Load Time | Load dashboard | < 2 seconds | ⬜ |
| P.2 | Product List Load | Load 100+ products | < 3 seconds | ⬜ |
| P.3 | No N+1 Queries | Check query count | Eager loading used | ⬜ |

---

## ✅ Test Execution Log

| Date | Tester | Section | Pass/Fail | Notes |
|------|--------|---------|-----------|-------|
| | | | | |

---

## 📝 Known Issues

| # | Issue | Severity | Status |
|---|-------|----------|--------|
| | | | |

---

## 🔄 Test Environment

- **Laravel Version**: 11.x
- **PHP Version**: 8.2+
- **Database**: MySQL 8.0+
- **Browser**: Chrome/Firefox/Safari (latest)

---

*Last Updated: January 13, 2026*
