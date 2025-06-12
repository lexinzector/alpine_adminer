<?php

/**
 * Adds the DB structure to the sql command page
 * @link https://www.adminer.org/plugins/#use
 * @author Emanuele "ToX" Toscano, https://github.com/tox82
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class DbStructure
{
    public function head()
    {
        if (strpos($_SERVER['QUERY_STRING'], '&sql=') !== false) {
            $tables = array_keys(Adminer\tables_list());
            $tableData = [];

            foreach ($tables as $table) {
                $engine = Adminer\table_status($table)['Engine'] ?? 'Unknown';
                $tableContent = "## TABLE: " . $table . " ($engine)\n";

                foreach (Adminer\fields($table) as $field) {
                    $type = $field["type"];
                    $name = $field["field"];
                    $null = $field["null"] ? "NULL" : "NOT NULL";
                    $auto = $field["auto_increment"] ? "AUTO_INCREMENT" : "";

                    $tableContent .= "- $name - $type $null $auto\n";
                }

                $indexes = Adminer\indexes($table);
                if (!empty($indexes)) {
                    $tableContent .= "\n### INDEXES:\n";
                    foreach ($indexes as $index) {
                        $type = $index["type"];
                        $columns = implode(", ", $index["columns"]);
                        $tableContent .= "- " . ($type === "PRIMARY" ? "PRIMARY KEY" : ($type === "UNIQUE" ? "UNIQUE INDEX" : "INDEX")) . " (" . $columns . ")\n";
                    }
                }

                $foreignKeys = Adminer\foreign_keys($table);
                if (!empty($foreignKeys)) {
                    $tableContent .= "\n### FOREIGN KEYS:\n";
                    foreach ($foreignKeys as $fk) {
                        $source = implode(", ", $fk["source"]);
                        $target = implode(", ", $fk["target"]);
                        $constraints = isset($fk["on_delete"]) ? " ON DELETE " . $fk["on_delete"] : "";
                        $constraints .= isset($fk["on_update"]) ? " ON UPDATE " . $fk["on_update"] : "";
                        $tableContent .= "- " . $source . " -> " . $fk["table"] . "(" . $target . ")" . $constraints . "\n";
                    }
                }

                $tableContent .= "\n";

                $tableData[] = [
                    'name' => $table,
                    'content' => $tableContent
                ];
            }

            echo '<script nonce="' . Adminer\get_nonce() . '">
                document.addEventListener("DOMContentLoaded", function() {
                    const tables = ' . json_encode($tableData) . ';
                    const container = document.createElement("div");
                    container.style.display = "flex";
                    container.style.flexDirection = "column";
                    container.style.marginTop = "70px";

                    const title = document.createElement("h2");
                    title.textContent = "DB Structure";
                    container.appendChild(title);

                    // Toggle buttons
                    const buttonContainer = document.createElement("div");
                    buttonContainer.style.display = "flex";
                    buttonContainer.style.flexWrap = "wrap";
                    buttonContainer.style.gap = "8px";
                    buttonContainer.style.marginBottom = "16px";

                    // Toggle all
                    const toggleAll = document.createElement("button");
                    toggleAll.textContent = "Show/hide all";
                    toggleAll.className = "adminer-button";
                    toggleAll.addEventListener("click", () => {
                        const allActive = Array.from(buttonContainer.querySelectorAll("button.table-toggle"))
                            .every(btn => btn.classList.contains("active"));

                        buttonContainer.querySelectorAll("button.table-toggle").forEach(btn => {
                            btn.classList.toggle("active", !allActive);
                        });
                        updateTextarea();
                    });
                    buttonContainer.appendChild(toggleAll);

                    // Table buttons
                    tables.forEach((table, index) => {
                        const btn = document.createElement("button");
                        btn.textContent = table.name;
                        btn.className = "adminer-button table-toggle active";
                        btn.dataset.tableIndex = index;
                        btn.addEventListener("click", function() {
                            this.classList.toggle("active");
                            updateTextarea();
                        });
                        buttonContainer.appendChild(btn);
                    });

                    // Textarea
                    const textarea = document.createElement("textarea");
                    textarea.id = "description";
                    textarea.style.width = "100%";
                    textarea.style.height = "400px";

                    // Update function
                    function updateTextarea() {
                        const activeTables = Array.from(buttonContainer.querySelectorAll("button.table-toggle.active"))
                            .map(btn => tables[btn.dataset.tableIndex]);
                        textarea.value = activeTables.map(t => t.content).join("\n");
                    }

                    // Build container
                    container.appendChild(buttonContainer);
                    container.appendChild(textarea);
                    document.getElementById("content").appendChild(container);

                    // Initial update
                    updateTextarea();
                });
            </script>';
        }
    }
}
