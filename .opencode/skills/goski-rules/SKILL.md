---
name: goski-rules
description: Git branch naming, commit format, and workflow conventions for GOSKI Laravel
---
# GOSKI Rules — Git and Workflow Conventions

## Branch naming
```
@{username}/{issue-number}/{type}/{kebab-case-name}
```
- Types: `feat`, `fix`, `chore`, `refactor`, `style`
- Examples:
  - `@carlosegoulart/24/fix/tailwind-css-configuration`
  - `@carlosegoulart/33/feat/ci-config-base`

## Commit naming (Conventional Commits)
```
<type>(<scope>): <subject>
```
- Types: `feat`, `fix`, `chore`, `refactor`, `style`, `build`, `test`, `docs`
- Scope (optional): area of the codebase, e.g. `deploy`, `seeder`, `ui`, `auth`
- Examples from existing history:
  - `feat: add multi-stage build step to compile and copy frontend assets in Dockerfile`
  - `fix(deploy): convert ARGs to ENVs with sane defaults for runtime availability`
  - `fix(seeder): resolve boolean type mismatch with PostgreSQL`
  - `chore(deps): bump guzzlehttp/guzzle from 7.12.0 to 7.12.1`
  - `build: update default PHP version from 7.4 to 8.1 in Dockerfile`

## Workflow
- **No direct pushes** to main/production — user pushes manually
- All changes go through branches and PRs
- Always get user approval before committing

## Commit grouping
Group related changes into minimal atomic commits:
- One `feat:` commit for a feature
- One `fix:` commit for a bugfix
- One `chore:` for dependencies or config
- Separate `test:` only for tests of already-shipped code
