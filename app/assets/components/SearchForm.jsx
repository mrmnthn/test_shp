import React, { Component } from "react";
import { Button, Divider, Form } from "semantic-ui-react";
import axios from "axios";
import ResultTable from "./ResultTable";

class SearchForm extends Component {
  constructor() {
    super();
    const initialState =  {
      airports: [],
      loading: true,
      from: {
        value:'',
        text:'',
      },
      to: {
        value: '',
        text: '',
      },
      bestFlights: [],
      stopoverOptions: [
        { text: "0", value: 0 },
        { text: "1", value: 1 },
        { text: "2", value: 2 },
      ]
    };
    this.state = initialState
  }

  componentDidMount() {
    this.getAirports();
  }

  getAirports() {
    axios.get(`http://localhost/api/airports`).then((airports) => {
      this.setState({ airports: airports.data, loading: false });
    });
  }

  getBestPrice() {
    const {value: fromVal} = this.state.from
    const {value: toVal} = this.state.to
    axios
      .get(`http://localhost/api/bestflights`, {
        params: {
          fromVal,
          toVal
        },
      })
      .then((bestFlights) => {
        this.setState({ bestFlights: bestFlights.data });
        console.log('bestflights', bestFlights)
      });
  }

  handleDropdownChange = (name, value, e) => {
    e.preventDefault();
    this.setState({ [name]: {value, text: e.target.innerText} });

    console.log(e.target.innerText);
  };

  onSubmit = (e) => {
    e.preventDefault();
    this.getBestPrice();
  };

  resetSearch = () => {
    this.setState({
      from: {
        value:'',
        text:'',
      },
      to: {
        value: '',
        text: '',
      },
      bestFlights: [],
    });
  }


  render() {
    const { airports, from, to, bestFlights, loading, stopoverOptions } = this.state
    return (
      <>
        <Form onSubmit={this.onSubmit}>
          <Form.Select
            loading={loading}
            value={from.value}
            name="from"
            fluid
            label="Departure Airport"
            options={airports}
            placeholder="Select an airport"
            onChange={(e, { value, name }) => this.handleDropdownChange(name, value, e)}
          />
          <Form.Select
            loading={loading}
            value={to.value}
            name="to"
            fluid
            label="Arrival Airport"
            options={airports}
            placeholder="Select an airport"
            onChange={(e, { value, name }) => this.handleDropdownChange(name, value, e)}
          />
          <Button 
            color="green" 
            type="submit" 
            disabled={!from.value
            || !to.value
            }
            >Search</Button>
          <Button type="reset"  color="pink"  onClick={this.resetSearch}>Reset</Button>
        </Form>
        <Divider />
        <ResultTable 
          bestFlights={bestFlights} 
          />
      </>
    );
  }
}
export default SearchForm;
