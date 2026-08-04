import React from "react";
import { View, Text, StyleSheet } from "react-native";

export default function ProfileScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Student Profile</Text>

      <View style={styles.content}>
        <Text style={styles.label}>Name</Text>
        <Text style={styles.text}>Orange S. Mercado</Text>

        <Text style={styles.label}>Course</Text>
        <Text style={styles.text}>BS Information Technology</Text>

        <Text style={styles.label}>Year Level</Text>
        <Text style={styles.text}>4th Year</Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#EAF4FF",
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },

  title: {
    fontSize: 28,
    fontWeight: "bold",
    color: "#1E3A8A",
    marginBottom: 25,
  },

  content: {
    backgroundColor: "#FFFFFF",
    width: "90%",
    padding: 20,
    borderRadius: 10,
  },

  label: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#1E3A8A",
    marginBottom: 5,
    marginTop: 10,
  },

  text: {
    fontSize: 16,
    color: "#333333",
    marginBottom: 10,
  },
});