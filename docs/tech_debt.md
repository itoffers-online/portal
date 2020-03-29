# Technical debt

This file describes [technical debt](https://en.wikipedia.org/wiki/Technical_debt) of the project. 

## Offer Thumbnail Project Logo

Offer thumbnail generator has hardcoded path to the local file upload storage. Because
of that it's impossible to use S3 or AzureBlob without breaking this feature. It should be fixed by 
pre fetching company logo from the storage (unless it's local storage) and store it in temp before generating
thumbnail.  