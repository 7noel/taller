# Cline Tools Mapping

Cline runs as a VS Code extension with no native Skill tool. Load skills by reading their SKILL.md directly with read_file.

| Action | Cline Tool |
|---|---|
| Read a file | read_file |
| Write/edit a file | write_to_file, replace_in_file |
| Run a shell command | execute_command |
| Search files | search_files, list_files |
| List definitions | list_code_definition_names |
| Fetch URL / search | execute_command (curl / Invoke-RestMethod) |
| Subagent dispatch | Not available; execute inline |
| Todos | task_progress parameter |
| Invoke a skill | Read SKILL.md with read_file |

## Loading a Skill

1. Locate SKILL.md at `.clinerules/skills/<skill-name>/SKILL.md`.
2. Read the full file with read_file.
3. Follow the skill's instructions exactly.

## Skill Locations

- `superpowers/` — Superpowers methodology (TDD, debugging, brainstorming, plans)
- `impeccable/` — UI/design skill pack (Impeccable)
- `findskill/` — Skill discovery meta-skill

## Notes

- Bootstrap rule (05-superpowers.md) loads at session start.
- Windows shell is PowerShell; use cmd /c for batch and npm.cmd instead of npm.