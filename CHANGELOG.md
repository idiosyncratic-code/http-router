# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.5.1] 2019-11-29
## Changed
- FastRoute is a suggestion instead of a requirement (but it's still required if you want to use the `RouteGroup` class).

## [0.5.0] 2019-11-29
Initial Release

### Added
- PSR-15 Request Handler
- RouteCollection interface for finding Route from incoming ServerRequest
- FastRoute based RouteGroup RouteCollection implementation
