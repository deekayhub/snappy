# Snappy Client User Guide

This guide explains how to use the Snappy Laravel application from the client side. It is written for non-technical users and covers the main role-based workflows in the system.

## What the system includes

- Public website pages for visitors
- Customer registration and customer portal
- Supplier registration and supplier portal
- Admin dashboard for managing users, jobs, quotes, categories, and dynamic fields

## Roles In The System

- Visitor: can browse the public site and register for an account
- Customer: can post jobs, review supplier quotes, manage job records, and rate suppliers
- Supplier: can browse the job board, submit quotes, monitor activity, and update their profile
- Admin or Superadmin: can manage the whole platform from the admin area

## Getting Started

### 1. Create an account

Use the correct registration page for your role:

- Customer registration: `/register/customer`
- Supplier registration: `/register/supplier`

The regular login page is used after registration:

- Login: `/login`

### 2. Verify your email

The application requires verified accounts before users can access protected areas. After signing up, complete email verification if prompted.

### 3. Log in

After login, the app sends you to the right area based on your role:

- Admin and superadmin users go to the admin dashboard
- Supplier users go to the supplier dashboard
- Customer users go to the customer dashboard

## Public Visitor Guide

Visitors can browse the public-facing pages before registering.

### Available pages

- Home page
- How It Works
- FAQ
- Contact Us
- Supplier information page

### How to register

1. Open the public site.
2. Choose either customer registration or supplier registration.
3. Fill in the form for your role.
4. Submit the form.
5. Verify your email if prompted.
6. Log in to access your dashboard.

## Customer Guide

Customers use the customer portal to post jobs and manage supplier responses.

### Customer navigation

The customer sidebar includes:

- Dashboard
- My Jobs
- Supplier Quotes
- Suppliers
- Profile

### A. Post a job

1. Log in as a customer.
2. Open `My Jobs` or the `Post a Job` form.
3. Click `Post New Job`.
4. Enter the job title.
5. Choose a category.
6. Fill in the organisation name, location, budget, needed-by date, and description.
7. If category-specific fields appear, complete those as well.
8. Click `Post Job`.
9. The job is saved and appears in `My Jobs`.

### B. Edit a job

1. Open `My Jobs`.
2. Find the job you want to change.
3. Click the edit icon.
4. Update the job details in the modal.
5. Save the changes.

### C. Delete a job

1. Open `My Jobs`.
2. Find the job you want to remove.
3. Click the delete icon.
4. Confirm the deletion.

### D. Review supplier quotes

1. Open `Supplier Quotes`.
2. Find the job with incoming quotes.
3. Review the supplier name, company details, price, delivery cost, discount, notes, and total.
4. Choose one of the available actions:
   - `Accept Quote`
   - `Reject Quote`
   - `Mark Pending`
   - `Mark Completed`
5. If you need to contact the supplier, use `Email Supplier`.

### E. Rate a supplier

1. Open a quote that has been marked `Completed`.
2. Select a star rating from 1 to 5.
3. Add an optional review.
4. Click `Submit` or `Update`.

### F. Browse suppliers

1. Open `Suppliers`.
2. Use the search box to find suppliers.
3. Review the list of companies, ratings, and review counts.
4. Click the view button to open supplier details.

### G. Update your profile

1. Open `Profile`.
2. Update your name, phone number, county, and school or club name.
3. Change your password if needed.
4. Save the changes.

## Supplier Guide

Suppliers use the supplier portal to browse jobs and submit quotes.

### Supplier navigation

The supplier sidebar includes:

- Dashboard
- Job Board
- Reports
- Activity
- Profile

### A. Complete your profile

1. Log in as a supplier.
2. Open `Profile`.
3. Fill in the company name and address.
4. Add your website link, review link, and social links if available.
5. Upload a company logo if needed.
6. Select the service categories you provide.
7. Save the profile.

### B. Browse jobs

1. Open `Job Board`.
2. Use the search box to find jobs by title, category, organisation, or location.
3. Filter by category if needed.
4. Sort by newest, oldest, ending soon, highest budget, or lowest budget.
5. Open a job card to review full details.

### C. Submit a quote

1. Open a job on the `Job Board`.
2. Click `Send Quote` or `Update Quote`.
3. Enter the quote amount for the job.
4. Add delivery cost if applicable.
5. Add any discount offered.
6. Add notes for the customer, such as delivery terms or timing.
7. Submit the quote.
8. The system saves the quote to that job.

### D. Update an existing quote

1. Return to the same job on the `Job Board`.
2. Open the quote form again.
3. Adjust the pricing or notes.
4. Submit the updated quote.

### E. Review activity and reports

1. Open `Activity` to see recent jobs and recent quotes.
2. Open `Reports` to view job counts by category and by location.

### F. Update supplier profile settings

1. Open `Profile`.
2. Change your company details, logo, or service categories.
3. Update social links as needed.
4. Save the profile.

## Admin Guide

Admins and superadmins manage the full platform.

### Admin navigation

The admin sidebar includes:

- Dashboard
- Active Jobs
- Purchase Quotes
- Supplier
- Customer
- Invoice
- Reports
- Categories
- Categories Fields

### A. Review the dashboard

1. Log in as an admin or superadmin.
2. Open `Dashboard`.
3. Review the total jobs, suppliers, and customers.
4. Check the recent job and quote summaries.

### B. Manage jobs

1. Open `Active Jobs`.
2. Review the job table.
3. Search through records if needed.
4. Use this screen as the central list of posted jobs for monitoring and support.

### C. Manage quotes

1. Open `Purchase Quotes`.
2. Review submitted quotes.
3. Check which jobs have quotes, the supplier who sent each quote, the customer who posted the job, the total amount, and the quote status.

### D. Manage suppliers

1. Open `Supplier`.
2. Search the supplier directory.
3. Review company name, email, phone, categories, address, website, and date added.
4. Delete supplier records when necessary.

### E. Manage customers

1. Open `Customer`.
2. Search the customer directory.
3. Review organisation categories, school or club name, county, and date added.
4. Delete customer records when necessary.

### F. Manage categories

1. Open `Categories`.
2. Create a new category by entering the name and selecting `Supplier` or `Customer`.
3. Update an existing category inline if needed.
4. Delete categories that are no longer required.

### G. Manage dynamic category fields

1. Open `Categories Fields`.
2. Click `Add New Field`.
3. Choose the supplier category the field belongs to.
4. Enter the field label and select the field type.
5. Optionally add field options, placeholder text, help text, and sort order.
6. Mark the field as required if needed.
7. Save the field.
8. Use `Edit` or `Delete` to maintain existing fields.

### H. Manage profile

1. Open the admin profile page.
2. Update your account information if required.
3. Save changes.

## Dynamic Fields On Jobs

Some job categories can have additional fields configured by the admin. These fields may appear as:

- Text
- Textarea
- Number
- Select
- Radio
- Checkbox
- File upload
- Date
- Time
- URL

When a customer chooses a category that has dynamic fields, the job form loads those fields automatically.

## Sample Demo Accounts

The project includes seeded demo users in the database seeder.

- Admin: `admin@snappyquote.test`
- Supplier: `supplier@snappyquote.test`
- Customer: `customer@snappyquote.test`
- Default password: `password`

## Notes For Clients

- Customer and supplier accounts are role-based, so users only see the menus for their role.
- The admin area is shared by both `admin` and `superadmin` roles.
- Customers can only rate suppliers after a quote has been marked completed.
- Supplier quotes are linked to a specific job and customer account.
- If a user cannot access a page, check whether the account has the correct role and a verified email address.
