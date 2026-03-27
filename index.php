<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>QuotesDB API</title>
</head>
<body>
  <h1>QuotesDB REST API</h1>
  <p>A PHP OOP REST API for famous and user-submitted quotations.</p>

  <h2>Available Endpoints</h2>

  <h3>Quotes</h3>
  <ul>
    <li>GET /api/quotes/</li>
    <li>GET /api/quotes/?id=1</li>
    <li>GET /api/quotes/?author_id=1</li>
    <li>GET /api/quotes/?category_id=1</li>
    <li>GET /api/quotes/?author_id=1&category_id=1</li>
    <li>GET /api/quotes/?random=true</li>
    <li>POST /api/quotes/</li>
    <li>PUT /api/quotes/</li>
    <li>DELETE /api/quotes/</li>
  </ul>

  <h3>Authors</h3>
  <ul>
    <li>GET /api/authors/</li>
    <li>GET /api/authors/?id=1</li>
    <li>POST /api/authors/</li>
    <li>PUT /api/authors/</li>
    <li>DELETE /api/authors/</li>
  </ul>

  <h3>Categories</h3>
  <ul>
    <li>GET /api/categories/</li>
    <li>GET /api/categories/?id=1</li>
    <li>POST /api/categories/</li>
    <li>PUT /api/categories/</li>
    <li>DELETE /api/categories/</li>
  </ul>
</body>
</html>