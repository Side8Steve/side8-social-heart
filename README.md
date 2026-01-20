# Side 8 Social Heart

Side 8 Social Heart is a WordPress plugin that provides a front-end portal for preparing and approving social content while delegating posting to the Side 8 engine.

## Features

- Front-end portal available via `[side8_social_heart]` shortcode.
- Roles: Social Author and Social Approver.
- Submissions and activity logs stored in WordPress custom post types.
- Admin settings page for tenant key, shared secret, and connection test.
- Signed HMAC SHA256 requests to the Side 8 engine.
- REST API routes for submissions and activity (nonce + capability gated).

## Setup

1. Install and activate the plugin.
2. Visit **Settings → Side 8 Social Heart**.
3. Enter your tenant key and shared secret.
4. Use **Test connection** to validate access.
5. Create a page and add the shortcode `[side8_social_heart]`.
6. Assign users to the **Social Author** or **Social Approver** role.

## Local testing guidance

- Ensure you are logged in with a user that has the Social Author or Social Approver role.
- Add the shortcode to a page and visit it on the front end.
- Use the form to submit a draft. Submissions are saved as `side8_social_submission` posts.
- Activity entries are appended in the `side8_social_activity` custom post type.

## Data storage

Submissions and activity logs are stored in two custom post types for simplicity and native WordPress querying. Statuses are tracked using custom post statuses (`side8_draft`, `side8_submitted`, `side8_approved`, `side8_queued`, `side8_posted`, `side8_failed`). If activity volume grows significantly, a custom table can be introduced later.

## Security

- All REST routes require valid capability checks and a REST nonce.
- The shared secret is stored in WordPress options and is never exposed in front-end scripts.
